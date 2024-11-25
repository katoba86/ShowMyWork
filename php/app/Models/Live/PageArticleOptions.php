<?php


namespace App\Models\Live;


use Illuminate\Database\Eloquent\Model;

class PageArticleOptions extends Model
{

    public $table = 'article_options';
    protected $guarded = [];





    public function usesTimestamps(): bool
    {
        return false;
    }

    public function article()
    {
        return $this->belongsTo(PageArticles::class,'article','id');
    }

}
