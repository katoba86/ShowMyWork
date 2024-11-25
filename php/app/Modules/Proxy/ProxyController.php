<?php


namespace App\Modules\Proxy;


use App\AppConfig;

use App\Models\Live\Page;
use App\Models\Live\PageArticles;

use App\Models\Live\PageCategory;
use App\Models\Live\PageMedia;
use App\Models\Live\PageTags;
use App\Modules\Image\RemoveImageCopyright;
use App\Modules\PageFaker;
use App\Modules\Transform\Chains\DOM\Clean;
use App\Modules\Transform\Chains\Internal\Links;
use App\Modules\Transform\Models\TransformRequest;
use App\Transformers\ArticleTransformer;
use App\Transformers\CategoryTransformer;
use App\Transformers\MediaTransformer;
use App\Transformers\PageTransformer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProxyController extends Controller
{

    private function prepareArticle(PageArticles &$pa)
    {



        $transformRequest = new TransformRequest($pa->content);
        $transformRequest->setOptions(['article'=>$pa]);







    }

    public function articles(Request $request)
    {
        /* @var $page Page */
        $page = $request->get('page');


        $articles = $page->articles;




        $articles = $articles->filter(function(PageArticles $pa){


            $pa->load('image');


            return ($pa->image !== null);
        })->map(function(PageArticles $pa){



            $this->prepareArticle($pa);
            $pa->featured_media = $pa->image->getKey();
            $referenceCategories = $pa->articleObject->categories->pluck('id')->map(fn($item)=>$item+10000)->toArray();
            $referenceTags = $pa->articleObject->tags->pluck('id')->toArray();




            $pa->tags = [...$referenceTags,...$referenceCategories];
            $pa->categories = [1];
            return $pa;
        });


        return fractal()->collection($articles)->transformWith(new ArticleTransformer())->toArray();
    }


    public function info(Request $request)
    {



        /* @var $page Page */
        $page = $request->get('page');
        $createRoute = function(string $path){

            $namespace = pathinfo($path,PATHINFO_DIRNAME);

          return [
                'namespace'=>($namespace===null)?'/':$namespace,
                'methods'=>["GET"],
                'endpoints'=>[],
                '_links'=>[]
          ];
        };


        $object = [
            'name'=>$page->getOption('title',$page->name),
            'title'=>$page->getOption('title',$page->name),
            'description'=>$page->getOption('description'),
            "gmt_offset"=>1,
            'home'=>"https://".$page->domain,
            "logo_title"=>$page->getOption('logo_title'),
            "home_banner"=>url('/media/'.$page->getOption('header')),
            "timezone_string"=>"Europe\/London",
            "routes"=>[
                "/"=>$createRoute("/"),
                "/wp/v2/posts"=>$createRoute("wp/v2/posts"),
                "/wp/v2/media"=>$createRoute("wp/v2/media"),
                "/wp/v2/categories"=>$createRoute("wp/v2/categories"),
                "/wp/v2/tags"=>$createRoute("wp/v2/tags"),
                "/wp/v2/pages"=>$createRoute("wp/v2/pages"),
            ]
        ];



        return $object;
    }


    public function media(Request $request)
    {


        /* @var $page Page */
        $page = $request->get('page');



        $sql="SELECT pm.* from page_media pm
LEFT JOIN page_articles pa on pa.id = pm.article AND pa.page = :page ";

        $allPagemedia = PageMedia::fromQuery($sql,[
            'page'=>$page->getKey()
        ]);

        $list = Storage::files();
        $pm = [];
        foreach($allPagemedia as $pageMedia){
            if(in_array($pageMedia->getKey().".jpg",$list)){
                $pm[] = $pageMedia;
            }
        }



        return fractal()->collection($pm)->transformWith(new MediaTransformer())->toArray();

    }

    public function tags(Request $request)
    {

        /* @var $page Page */
        $page = $request->get('page');
        $tags = $page->tags;


        $counts = collect(DB::select('Select DISTINCT count(pool_article_id) as anzahl,pool_tag_id from pool_article_tags pat WHERE pat.pool_article_id IN (Select article from page_articles) GROUP BY pool_tag_id ORDER BY anzahl DESC;'));
        $counts = $counts->keyBy('pool_tag_id');


        $tags->map(function(PageTags $tag) use ($counts){


            $count = $counts->get($tag->pool_tag);
            if(null !== $count){
                $count = $count->anzahl;
            }else{
                $count = 1;
            }

            $tag->count = $count;


            return $tag;
        });



        $categories = $page->categories->map(function(PageCategory $item){
            $item->id+=10000;
            return $item;
        });






        return fractal()->collection([
            ...$tags,...$categories
        ])->transformWith(new CategoryTransformer())->toArray();
    }


    public function pages(Request $request)
    {

        /* @var $page Page */
        $page = $request->get('page');
        return fractal()->collection(PageFaker::genStaticPage($page))->transformWith(new PageTransformer())->toArray();
    }

    public function categories(Request $request,ProxyService $proxyService)
    {
        /* @var $page \App\Models\Live\Page */


        $category = new PageCategory();
        $category->id = 1;
        $category->slug = "default";
        $category->parent = 0;
        $category->name = "Default";
        $category->description = "";
        $category->count = 1;

        //$page = $request->get('page');
        return fractal()->collection([$category])->transformWith(new CategoryTransformer())->toArray();
    }


}
