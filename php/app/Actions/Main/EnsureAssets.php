<?php


namespace App\Actions\Main;


use App\Models\Live\Page;
use App\Models\Live\PageArticles;
use App\Models\Live\PageCategory;
use App\Models\Live\PageTags;
use App\Models\Pool\PoolTagOrCategory;
use App\Models\Pool\PoolArticle;
use App\Models\Pool\PoolCategory;
use App\Models\Pool\PoolTag;
use Lorisleiva\Actions\Concerns\AsAction;

class EnsureAssets
{
    use AsAction;


    public function workOnTags(Page $page,PoolArticle $article,PageArticles $pageArticle)
    {
        $tags = $article->tags->pluck('id')->toArray();
        $existing = PageTags::whereIn('pool_tag',$tags)->get();
        //Check existing

        $difference = array_diff($tags,$existing->pluck('pool_tag')->toArray());

        foreach ($difference as $diff) {


            $tag = $article->tags()->where(['pool_tags.id'=> (int)$diff])->firstOrFail();
            if (!($tag instanceof PoolTag)) {
                throw new \RuntimeException('Pool and page error');
            }



            $tag = TranslateAsset::make()->handle($page, $tag);

            PageTags::create([
                'page' => $pageArticle->page,
                'pool_tag' => $diff,
                'isPrimary' => 0,
                'slug' => $tag->slug,
                'description' => $tag->description,
                'name' => $tag->name
            ]);

        }
    }

    public function workOnCategories(Page $page,PoolArticle $article,PageArticles $pageArticle)
    {
        $categories = $article->categories->pluck('id',)->toArray();
        $existing = PageCategory::whereIn('pool_category',$categories)->get();
        //Check existing

        $difference = array_diff($categories,$existing->pluck('pool_category')->toArray());

        foreach ($difference as $diff) {


            $cat = $article->categories()->where('pool_categories.id', (int)$diff)->firstOrFail();
            if (!($cat instanceof PoolCategory)) {
                throw new \RuntimeException('PoolCat and page error');
            }

            if(!($cat instanceof PoolTagOrCategory)) {
                throw new \RuntimeException('Invalid data provided');
            }

            $cat = TranslateAsset::make()->handle($page, $cat);

            PageCategory::create([
                'page' => $pageArticle->page,
                'pool_category' => $diff,
                'isPrimary' => 0,
                'slug' => $cat->slug,
                'description' => $cat->description,
                'name' => $cat->name
            ]);
        }
    }


    public function handle(Page $page,PageArticles $pageArticle)
    {

        $article = PoolArticle::findOrFail($pageArticle->article);


        $this->workOnLinks($pageArticle);
        $this->workOnTags($page,$article,$pageArticle);
        $this->workOnCategories($page,$article,$pageArticle);
    }

    private function workOnLinks(PageArticles $pageArticle)
    {

    }


}
