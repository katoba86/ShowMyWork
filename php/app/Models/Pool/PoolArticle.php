<?php


namespace App\Models\Pool;


use App\Actions\Main\ExtractKeywords;
use App\Models\Live\Page;
use App\Models\Live\PageArticles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\PoolArticle
 *
 * @property int id
 * @property int iid
 * @property string slug
 * @property string title
 * @property string content
 * @property string meta
 * @property string excerpt

 * @property string language
 * @mixin \Eloquent
 */
class PoolArticle extends Model implements PoolModel
{
    use HasFactory;


    protected $translatable = ['content', 'excerpt', 'title','slug'];


    protected $table = 'pool_articles';
    protected $guarded = [];

    protected $primaryKey = 'id';



    public function categories(){
        return $this->belongsToMany(PoolCategory::class,'pool_article_category','pool_article_id','pool_category_id');
    }
    public function page(){
        return $this->belongsTo(Page::class,'page','id');
    }

    public function site(){
        return $this->belongsTo(PoolSite::class,'pool','id');
    }

    public function tags(){
        return $this->belongsToMany(PoolTag::class,'pool_article_tags','pool_article_id','pool_tag_id');
    }





    public function scopeUnused(Builder $query,int $poolId):Builder
    {
        $table = app(PageArticles::class)->getTable();
        return $query->select($this->table.".*")->
            leftJoin($table,'article','=',$table.'.id')->
            whereNull('article')->where('pool',$poolId)->orderByDesc('id');
    }




    public function scopeStatus($query,string $status)
    {
        return $query->where('status',$status);
    }




    public function scopeLanguage($query,string $language)
    {
        return $query->where('language',$language);
    }


    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    public $casts = [
        'meta'=>'array'
    ];

}
