<?php


namespace App\Actions\Main;


use App\AppConfig;
use App\Models\Live\PageArticles;
use App\Models\Live\Page;
use App\Models\Pool\PoolArticle;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class AddToPage
{
    public string $commandSignature     = 'app:add {page}';
    public string $commandDescription   = 'Add one entry';


    use AsAction;


    public function handle(Page $page)
    {


       $article = GetPossibleArticle::make()->handle($page,5)->first();

        $article = TranslateArticle::make()->handle($page,$article);
        $pageArticle = $page->articles()->create([
            'article'=>$article->id,
           'slug'=>$article->slug,
            'content'=>$article->content,
            'title'=>$article->title,
            'excerpt'=>$article->excerpt
        ]);



        /* @var $pageArticle PageArticles */
        EnsureAssets::make()->handle($page,$pageArticle);

        return null;
    }

    public function asCommand(Command $command)
    {
        $page = Page::findOrFail((int)$command->argument('page'));
        $this->handle($page);
    }



}
