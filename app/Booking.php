<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use Uuids;
    use SoftDeletes;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = ['schedule','discount'];

    protected $table = 'bookings';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function discount(){
        return $this->belongsToMany(Discount::class);
    }
    
    
    public function products(){
        return $this->belongsToMany(ProductCategory::class,'product_booking','booking_id','product_id');
    }
    
    
    public function extra(){
        return $this->belongsToMany(Extra::class);
    }

    public function attribute(){
        return $this->belongsToMany(AttributeName::class);
    }

    public function attributeName(){
        return $this->belongsToMany(AttributeName::class);
    }

    public function service(){
        return $this->belongsToMany(Service::class);
    }
    
    public function price(){
        return $this->belongsToMany(Price::class);
    }

    public function vendor(){
        return $this->belongsToMany(User::class,'booking_vendor');
    }

    public function task(){
        return $this->hasMany(Task::class);
    }

    public function getBasePriceAttribute(){
        
        return 
              $this->calcPrice($this->no_of_rooms)
            + $this->service()->sum('price')
            + $this->extra()->sum('price');
    }

    public function serviceAttribute()
    {
        return $this->belongsToMany(ServiceAttribute::class)->withPivot('service_base_amount');
    }

    public function calcPrice($unit)
    {
        switch ($unit) {
            case 1:
                return $this->price()->first()['one'];
                break;
            case 2:
                return $this->price()->first()['two'];
                break;

            case 3:
                return $this->price()->first()['three'];
                break;   

            case 4:
                return $this->price()->first()['four'];
                break;

            case 5:
                return $this->price()->first()['five'];
                break;

            case 6:
                return $this->price()->first()['six'];
                break;

            case 7:
                return $this->price()->first()['seven'];
                break;

            case 8:
                return $this->price()->first()['eight'];
                break;

            case 9:
                return $this->price()->first()['nine'];
                break;

            case 10:
                return $this->price()->first()['ten'];
                break;

            default:
                return $this->price()->first()['default'];
                break;
        }
    }
}
