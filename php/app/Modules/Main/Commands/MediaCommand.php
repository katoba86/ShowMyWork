<?php


namespace App\Modules\Main\Commands;

use App\Actions\Main\AssignImage;
use App\Models\Live\PageArticles;
use App\Models\Live\Page;
use App\Models\Pool\PoolSite;
use Illuminate\Console\Command;

class MediaCommand extends Command
{
    public $signature = 'app:media {pid}';
    public $description = 'Add medias to page Articles';



    public function handle(){
        $pid = (int)$this->argument('pid');
        /* @var $page Page */
        $page = Page::findOrFail($pid);



       $sql="SELECT page_articles.id,page_articles.article from page_articles
LEFT JOIN page_media ON page_media.article = page_articles.id
WHERE page_articles.page = ? AND ISNULL(link) ";
       $records = \DB::select($sql,[
           $page->id
       ]);


        if(count($records) === 0){
            $this->getOutput()->text('Nothing to do');
            exit(0);
        }
       foreach($records as $record){


            $pageArticle = PageArticles::where("id",$record->id)->firstOrFail();
            AssignImage::assignImage($pageArticle);

       }

    }
}
