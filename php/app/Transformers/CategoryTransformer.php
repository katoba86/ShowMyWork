<?php
namespace App\Transformers;

use App\Models\Live\PageAsset;
use App\Models\Live\PageCategory;
use App\Models\Live\PageTags;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{

    /**
     * @param PageAsset|PageCategory|PageTags $category
     * @return array
     */
    public function transform(PageAsset $category): array
    {

       $id = (isset($category->pool_tag))?(int)$category->pool_tag:$category->pool_category;


       if($id === null){$id = 1;}

        return [
            'name'=>$category->name,
            'slug'=>$category->slug,
            'taxonomy'=>'category',
            'parent'=>0,
            'parentitem'=>0,
            "count"=>(isset($category->count) && null!==$category->count)?$category->count:1,
            'id'=>$id,
            'wid'=>$id,
            'description'=>$category->description
        ];
    }

}
