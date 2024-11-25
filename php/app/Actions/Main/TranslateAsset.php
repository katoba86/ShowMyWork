<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Pool\PoolModel;
use App\Models\Pool\PoolTagOrCategory;
use App\Modules\Transform\Chains\Content\Minify;
use App\Modules\Transform\Chains\Content\ReMinify;
use App\Modules\Transform\Chains\Content\Translate;
use App\Modules\Transform\Chains\DOM\Clean;
use App\Modules\Transform\TransformService;
use Lorisleiva\Actions\Concerns\AsAction;

class TranslateAsset
{

    use AsAction;



    public function handle(Page $page,PoolTagOrCategory $tag)
    {

        if(!($tag instanceof PoolModel)){
            throw new \RuntimeException('Cant transform PoolCategory without being a Poolmodel');
        }


        $transformService = new TransformService();
        $transformService->setOptions([
            'language'=>$page->targetLanguage,
            'page'=>$page
        ]);

        $transformService->addRunner(new Clean());
        $transformService->addRunner(new Minify());
        $transformService->addRunner(new Translate());
        $transformService->addRunner(new ReMinify());


        $tag = $transformService->transform($tag,'name');
        $tag->slug = $transformService->buildSlug($tag->name,$page->targetLanguage);
        if(strlen($tag->description)>=1) {
            $tag = $transformService->transform($tag, 'description');
        }

        return $tag;
    }




}
