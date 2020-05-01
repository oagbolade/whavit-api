<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAttribute extends Model
{
     use Uuids;
     use SoftDeletes;

        /**
         * Indicates if the IDs are auto-incrementing.
         *
         * @var bool
         */


    public $incrementing = false;

    public function booking(){
        return $this->belongsToMany(Booking::class);
    }

    public function attributeName(){
        return $this->belongsTo(AttributeName::class);
    }
}
