<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Pool\PoolArticle;
use App\Modules\Transform\Chains\Content\Minify;
use App\Modules\Transform\Chains\Content\ReMinify;
use App\Modules\Transform\Chains\Content\RemoveImageCopyright;
use App\Modules\Transform\Chains\Content\Translate;
use App\Modules\Transform\Chains\DOM\Clean;
use App\Modules\Transform\TransformService;
use Lorisleiva\Actions\Concerns\AsAction;

class TranslateArticle
{

    use AsAction;



    public function handle(Page $page,PoolArticle $article)
    {

        $transformService = new TransformService();
        $transformService->setOptions([
            'language'=>$page->targetLanguage,
            'page'=>$page
        ]);


        $cleanImages = new Clean();
        $cleanImages->setRemoveTags(["img","figure","svg"]);

        $transformService->addRunner(new Clean());
        $transformService->addRunner(new RemoveImageCopyright());
        $transformService->addRunner($cleanImages);
        $transformService->addRunner(new Minify());
        $transformService->addRunner(new Translate());
        $transformService->addRunner(new ReMinify());



        $article = $transformService->transform($article,'title');
        $article->slug = $transformService->buildSlug($article->title,$page->targetLanguage);
        $article = $transformService->transform($article,'content');
        $article = $transformService->transform($article,'excerpt');

        return $article;
    }




}
