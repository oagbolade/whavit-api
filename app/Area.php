<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use Uuids;
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected  $fillable = ['title'];
    
    public function products(){
        return $this->belongsToMany('App\ProductCategory','category_area','area_id','category_id');
    }

    public function clean(){
        return $this->hasMany('App\Clean');
    }
}
