<?php

namespace App\Http\Controllers\API\User;

use App\User;
use App\Mail\SendReferralCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    public function sendReferralLink($email)
    {
        $user = Auth()->user();

        $referralCode = $user->referral_code;

        try {
            Mail::to($email)->send(new SendReferralCode($referralCode));
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (isset($error)) {
            return response()->json([
                'message' => $error
            ], 500);
        } else {
            return response()->json([
                'message' => 'mail sent'
            ], 200);
        }
    }
}
