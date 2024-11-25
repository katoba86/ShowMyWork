<?php


namespace App\Models\Live;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Live\Page
 *
 * @property int id
 * @property string domain
 * @property string targetLanguage
 * @property mixed catchphrase
 * @mixin \Eloquent
 */
class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';
    protected $primaryKey = 'id';



    public function articles(): HasMany
    {
        return $this->hasMany(PageArticles::class,'page','id');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(PageTags::class,'page','id');
    }
    public function categories(): HasMany
    {
        return $this->hasMany(PageCategory::class,'page','id');
    }

    public function getOption(string $key,$notFound = null)
    {
        $option = $this->options()->where(["key"=>$key])->first();
        if($option instanceof PageOptions){
            return $option->value;
        }
        return $notFound;
    }

    public function options()
    {
        return $this->hasMany(PageOptions::class,'page','id');
    }


    public function getRouteKeyName()
    {
        return 'id';
    }

    /* ------- new Version ------------- */

    public function keywords(){
        return $this->hasMany(PageKeywords::class,'page','id');
    }

    public function pageKeyword()
    {
        return $this->keywords();
    }


}
