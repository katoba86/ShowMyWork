<?php


namespace App\Modules;


use App\Models\Live\Page;
use App\Models\Live\PageArticles;
use App\Models\Pool\PoolArticle;
use App\Modules\Markdown\BetterMarkdown;
use Str;

class PageFaker
{

    /**
     * @return PoolArticle[]
     */
    public static function genStaticPage(Page $page):array
    {
        $articles = [];

        $data = \Storage::disk('static')->files($page->targetLanguage.DIRECTORY_SEPARATOR);
        foreach($data as $k=>$file){

            $rsp = BetterMarkdown::loadByFile($file);

            $poolArticle = new PageArticles();
            $poolArticle->id = (int)($k+1);
            $poolArticle->slug = Str::slug($rsp->getMetaTag('title'));
            $poolArticle->content = $rsp->getContent();
            $poolArticle->title = $rsp->getMetaTag('title');
            $articles[] = $poolArticle;
        }


         return $articles;
    }


}
