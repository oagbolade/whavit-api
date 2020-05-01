<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorTraining extends Model
{
    protected  $fillable = ['user_id','training_date','training_time'];
}
