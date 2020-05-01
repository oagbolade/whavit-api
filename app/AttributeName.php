<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeName extends Model
{
    use Uuids;
    use SoftDeletes;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $table = 'attribute_names';

    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function attribute(){
        return $this->hasMany(ServiceAttribute::class);
    }
}
