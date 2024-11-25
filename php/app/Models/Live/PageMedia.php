<?php


namespace App\Models\Live;


use http\Url;


class PageMedia extends Model
{
    public string $table='page_media';
    public array $guarded = [];


    public function articleObject()
    {
        return $this->belongsTo(PageArticles::class,'id','article');
    }


    public function getUpdated()
    {
        return date("Y-m-d\TH:i:s",strtotime($this->updated_at));
    }

    public function createLink()
    {
        return url("/media/".$this->getKey().".jpg");
    }

}
