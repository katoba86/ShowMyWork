<?php

namespace App\Models\Live;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string keyword
 * @property mixed language
 */
class PageKeywords extends Model
{
    use HasFactory;

    public string $id = 'id';
    protected $table='page_keywords';



    public function usesTimestamps()
    {
        return false;
    }


    public function page():BelongsTo{
        return $this->belongsTo(Page::class,'page','id');
    }





}
