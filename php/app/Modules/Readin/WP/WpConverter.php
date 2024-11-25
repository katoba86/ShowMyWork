<?php


namespace App\Modules\Readin\WP;


use App\Models\Pool\PoolArticle;
use App\Models\Pool\PoolCategory;
use App\Models\Pool\PoolSite;
use App\Models\Pool\PoolTag;
use App\Modules\Readin\Interfaces\ConverterInterface;
use GuzzleHttp\Client;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;

class WpConverter implements ConverterInterface
{


    /**
     * @var WpAPI
     */
    private WpAPI $client;
    private PoolSite $page;

    public function __construct(PoolSite $page)
    {
        $this->client = new WpAPI($page->domain,new Client());
        $this->page = $page;
    }

    public function getCategories(): void
    {
        $categories = $this->client->categories();



        $data = collect($categories["results"]);
        $data->each(fn($item) => $this->saveCategory($item));
    }

    private function saveCategory($item)
    {

        $category = new PoolCategory();
        $category->iid = (int)$item["id"];
        $category->name = $item["name"];
        $category->slug = $item["slug"];
        $category->parent = (int)$item["parent"];
        $category->description = $item["description"];
        try {
            $this->page->categories()->save($category);
        }catch(QueryException $exception){
            if((int)$exception->getCode()===23000){
                return;
            }
            throw $exception;
        }
    }

    public function getTags(): void
    {
        $tags = $this->client->tags();
        $data = collect($tags["results"]);
        $data->each(fn($item) => $this->saveTag($item));
    }

    private function saveTag($item)
    {

        $tag = new PoolTag();
        $tag->iid = (int)$item["id"];
        $tag->name = $item["name"];
        $tag->slug = $item["slug"];
        $tag->description = $item["description"];

        try {
            $this->page->tags()->save($tag);
        }catch(QueryException $exception){
            if((int)$exception->getCode()===23000){
                return;
            }
            throw $exception;
        }
    }

    public function getPosts(): void
    {
        $posts = $this->client->posts();
        $data = collect($posts["results"]);
        $data->each(fn($item) => $this->savePost($item));


    }

    public function getMetaInformations(): void
    {
        // TODO: Implement getMetaInformations() method.
    }



    public function start()
    {
        $this->getCategories();
        $this->getTags();
        $this->getPosts();
    }

    private function savePost($item)
    {

        $article = new PoolArticle();
        $article->iid = (int)$item["id"];
        $article->slug = $item["slug"];
        $article->title = $item["title"]["rendered"];
        $article->excerpt = $item["excerpt"]["rendered"];
        $article->meta = [];
        $article->content = $item["content"]["rendered"];


        if(count($item["categories"])>0) {
            $categories = PoolCategory::where('pool',$this->page->id)->whereIn('iid', $item["categories"])->get();

            if (count($categories) !== count($item["categories"])) {

                throw new \RuntimeException("Count of categories and given categories does not match.
                    Got ".count($categories)." but detected ".count($item["categories"])."
                This is a bug!");
            }
        }else{
            $categories = [];
        }
        if(count($item["tags"])>0) {
            $tags = PoolTag::where('pool',$this->page->id)->whereIn('iid', $item["tags"])->get();
            if (count($tags) !== count($item["tags"])) {
                throw new \RuntimeException('Count of tags and given tags does not match. This is a bug!');
            }
        }else{
            $tags = [];
        }


        try{
            $article = $this->page->articles()->save($article);
            if(count($categories)>0) {
                foreach ($categories as $category) {
                    $article->categories()->attach($category);
                }
            }
            if(count($tags)>0) {
                foreach ($tags as $tag) {
                    $article->tags()->attach($tag);
                }
            }
        }catch(QueryException $exception){
            if((int)$exception->getCode()===23000){
                return;
            }
            throw $exception;
        }catch(\ErrorException $e){
            echo "<pre>";print_r($article);exit();
        }




    }

    /**
     * @deprecated
     * @return string|null
     */
    public function getLanguageOfPage():?string
    {
        $data = Http::get($this->page->domain);
        $content = $data->body();
        preg_match("/<html.*?lang=\"(.*?)\"/i",substr($content,0,500),$matches);
        if($matches){
            return strtolower(substr($matches[1],0,2));
        }
        return $this->page->sourceLang;
    }


}
