<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use Uuids;
    use SoftDeletes;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    protected $fillable = ['name', 'alias'];
    // protected $attributes = ['price_total' => 0];
    public function area()
    {
        return $this->belongsToMany(Area::class, 'area_product', 'area_id', 'product_id');
    }

    public function service()
    {
        return $this->belongsToMany(Service::class, 'product_service', 'product_id', 'service_id');
    }

    public function extra()
    {
        return $this->belongsToMany(Extra::class, 'extra_product', 'product_id', 'extra_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'product_booking', 'product_id', 'booking_id');
    }

    public function discount()
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id');
    }

    public function scopePrice()
    {
        return $this->hasOne(Price::class, 'product_id');
    }

    public function scopeGetPrice()
    {
        return $this->service()->sum('price')
            + $this->extra()->sum('price');
    }


    // public function scopeGetTotalFromUnit($query, $unit)
    // {
    //     switch ($unit) {
    //         case 1:
    //             return $this->price()->first()['one'];
    //             break;
    //         case 2:
    //             return $this->price()->first()['two'];
    //             break;

    //         case 3:
    //             return $this->price()->first()['three'];
    //             break;

    //         case 4:
    //             return $this->price()->first()['four'];
    //             break;

    //         case 5:
    //             return $this->price()->first()['five'];
    //             break;

    //         case 6:
    //             return $this->price()->first()['six'];
    //             break;

    //         case 7:
    //             return $this->price()->first()['seven'];
    //             break;

    //         case 8:
    //             return $this->price()->first()['eight'];
    //             break;

    //         case 9:
    //             return $this->price()->first()['nine'];
    //             break;

    //         case 10:
    //             return $this->price()->first()['ten'];
    //             break;

    //         default:
    //             return $this->price()->first()['default'];
    //             break;
    //     }
    // }

    // public function getPriceAttribute(){
    //     return $this->getPrice();
    // }
}
