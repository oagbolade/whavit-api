<?php

namespace App;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerificationCode extends Model
{
    use Uuids;
    use SoftDeletes;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    public function assignCode(User $user)
    {
        $this->user_id = $user->id;
        $this->expires_at = Time() + 1800;
        $this->verification_code = str_random(35);

        $this->save();
    }

    public function code(){
        return $this->verification_code;
    }
}
