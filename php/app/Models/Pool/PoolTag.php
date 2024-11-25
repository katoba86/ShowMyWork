<?php


namespace App\Models\Pool;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PoolPage
 *
 * @property int id
 * @property int iid
 * @property int parent
 * @property int pool
 * @property string slug
 * @property string description
 * @property string name
 * @property mixed status
 * @property mixed language
 * @mixin \Eloquent
 */
class PoolTag extends Model implements PoolModel,PoolTagOrCategory
{
    protected $table = 'pool_tags';
    protected $primaryKey = 'id';





}
