<?php


namespace App\Models\Live;


use Illuminate\Database\Eloquent\Model;

class PageOptions extends Model
{

    public $table = 'page_options';
    public $timestamps = false;
    public $guarded = [];


    public function page()
    {
        return $this->belongsTo(Page::class,'page','id');
    }

}
