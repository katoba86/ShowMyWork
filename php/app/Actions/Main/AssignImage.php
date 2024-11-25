<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Live\PageArticles;
use App\Models\Pool\PoolArticle;
use App\Modules\Image\ImageService;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignImage
{


    use AsAction;

    public string $commandSignature = 'app:media {page}';
    public string $commandDescription = 'Ensure all published articles have at least one image';


    public function prepareForCli(Page $page)
    {

        $sql="SELECT page_articles.id,page_articles.article from page_articles
LEFT JOIN page_media ON page_media.article = page_articles.id
WHERE page_articles.page = ? AND ISNULL(link) ";


        $records = \DB::select($sql,[
            $page->id
        ]);


        if(count($records) === 0){
            exit(0);
        }
        foreach($records as $record){
            $pageArticle = PageArticles::where("id",$record->id)->firstOrFail();
            $this->handle($pageArticle);
        }

    }

    public static function handle(PageArticles $article){


        /* @var $poolArticle PoolArticle */
        $poolArticle =  $article->articleObject()->first();




        if(strlen($poolArticle->title)>100){
            $keywords = \App\Modules\Keyword\ExtractKeywords::runForString($poolArticle->title,5,$poolArticle->site->sourceLanguage);
            $target = "";

            foreach($keywords as $keyword){
                if(strlen($target)+strlen($keyword)<100){
                    $target.=" ".$keyword;
                }else{
                    break;
                }
            }
        }else{
            $target = $poolArticle->title;
        }


        $image = new ImageService();
        $image->setKeywords([$target]);
        $images = $image->run();
        if($images->count() > 0){
            /* @var $targetImage \App\Modules\Image\ImageResponse */
            $targetImage = $images->random();

            $media = $article->assignImage($targetImage);
            DownloadImage::dispatch($media);

        }else{
          return null;
        }
    }

    public function asCommand(Command $command)
    {
        $page = Page::findOrFail((int)$command->argument('page'));
        $this->prepareForCli($page);
    }

}
