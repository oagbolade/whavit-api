<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use Uuids;
    use SoftDeletes;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    protected $fillable = ['title','description'];

    public function booking(){
        return $this->belongsToMany(Booking::class);
    }

    public function attribute(){
        return $this->hasMany(ServiceAttribute::class);
    }

    public function attributeName(){
        return $this->hasMany(AttributeName::class);
    }

}
