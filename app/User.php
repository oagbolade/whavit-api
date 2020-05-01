<?php

namespace App;

use App\Traits\Uuids;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Wallet;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Uuids;
    use HasRoles;
    use SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    const ADMIN_ONE = 'admin_one';
    const ADMIN_TWO  = 'admin_two';
    const USER = 'user';
    const BUSINESS_REP = 'business';
    const VENDOR =  'vendor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email','type','mobile_number', 'token', 'password', 'type', 'referral_code', 'business_name'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    //Get the identifier that will be stored in the subject claim of the JWT.
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    // Return a key value array, containing any custom claims to be added to the JWT.
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdminOne()
    {
        return $this->type === self::ADMIN_ONE;
    }

    public function isAdminTwo(){
        return $this->type === self::ADMIN_TWO;
    }

    public function isRep(){
        return $this->type === self::BUSINESS_REP;
    }

    public function isVendor(){
        return $this->type === self::VENDOR;
    }

    public function isUser(){
        return $this->type === self::USER;
    }

    public function scopeVendor($query){
        return $query->where('type','vendor');
    }

    public function scopeOnline($query){
        return $query->where('availability',true);
    }

    public function scopeLocated($query,$location){
        return $query->where('location',$location);
    }

    public function  scopeNotBooked($query){
        return $query->where('booking_status',false);
    }

    public function transaction(){
        return $this->hasMany('App\Transaction');
    }

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function card(){
        return $this->hasMany(Card::class);
    }

    public function verificationToken()
    {
        return $this->hasOne(VerificationToken::class);
    }

    public function bank(){
        return $this->hasMany(Bank::class);
    }

    public function review(){
        return $this->hasMany(Review::class);
    }
  
    public function booking(){
        return $this->hasMany(Booking::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function vendorBooking(){
        return $this->belongsToMany(Booking::class,'booking_vendor');
    }

    public function referral(){
        return $this->hasMany(User::class,'referred_by_id');
    }

    public function referred_by(){
        return $this->belongsTo(User::class,'referred_by_id');
    }
}
