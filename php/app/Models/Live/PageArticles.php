<?php


namespace App\Models\Live;


use App\Models\Pool\PoolArticle;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed article
 * @property mixed content
 * @property mixed slug
 */
final class PageArticles extends Model
{

    public $table = 'page_articles';


    protected $guarded = [];


    public function page(){
        return $this->hasOne(Page::class,'id','page');
    }

    public function options()
    {
        return $this->hasMany(PageArticleOptions::class,'article','id');
    }

    public function getOption(string $key,$notFound = null)
    {
        $option = $this->options()->where(["key"=>$key])->first();
        if($option instanceof PageArticleOptions){
            return $option->value;
        }
        return $notFound;
    }

    public function articleObject(){
        return $this->belongsTo(PoolArticle::class,'article','id');
    }


    public function image()
    {
        return $this->belongsTo(PageMedia::class,'id','article');
    }

    public function assignImage(\App\Modules\Image\ImageResponse $targetImage)
    {

        $pageMedia = new PageMedia();
        $pageMedia->article = $this->id;
        $pageMedia->link = $targetImage->full;
        $pageMedia->infos = json_encode($targetImage);
        $pageMedia->save();
        $pageMedia->refresh();
        return $pageMedia;
    }

}
