<?php


namespace App\Models\Pool;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PoolPage
 *
 * @property int id
 * @property string domain

 * @property mixed sourceLanguage
 * @mixin \Eloquent
 */
class PoolSite extends Model
{

    use HasFactory;

    protected $table = 'pool_site';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public function tags(): HasMany
    {
        return $this->hasMany(PoolTag::class,'pool','id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(PoolCategory::class,'pool','id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(PoolArticle::class,'pool','id');
    }

    public function poolArticle()
    {
        return $this->articles();
    }


    public function getDomain()
    {
        return parse_url($this->domain,PHP_URL_HOST);
    }

}
