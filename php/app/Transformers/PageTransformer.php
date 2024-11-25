<?php
namespace App\Transformers;

use App\Models\Live\PageArticles;
use App\Models\Pool\PoolArticle;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{

    /**
     * @param PageArticles $article
     * @return array
     */
    public function transform(PageArticles $article): array
    {

        $date = date("Y-m-d\TH:i:s",time());


        return [

            'date'=>$date,
            'date_gmt'=>$date,
            'modified'=>$date,
            'modified_gmt'=>$date,
            'guid'=>['rendered'=>$article->getKey()],
            'status'=>'publish',
            'type'=>'page',



            'slug'=>$article->slug,
            'id'=>$article->getKey(),
            'wid'=>$article->getKey(),


            'excerpt'=>["rendered"=>$article->excerpt],
            'content'=>["rendered"=>$article->content],
            'title'=>["rendered"=>$article->title]
        ];
    }

}
