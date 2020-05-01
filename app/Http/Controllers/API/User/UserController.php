<?php

namespace App\Http\Controllers\API\User;

use App\User;
use Illuminate\Support\Facades\Hash;
use App\VerificationCode;
use App\Mail\SendResetCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\UpdateProfileRequest;
use App\Review;
use App\Mail\SendReferralCode;

class UserController extends Controller
{
    public function userDetails()
    {
        return response()->json(Auth()->user());
    }

    public function validateResetCode(Request $request, $code)
    {
        $this->validate($request, [
            'password' => 'required'
        ]);
        $code = VerificationCode::where('verification_code', $code)->first();
        if (!$code->exists()) {
            return response()->json([
                'message' => 'Invalid verification code'
            ], 422);
        } else {
            $password = $request->password;
            $user = User::where('id', $code->user_id)->first();

            // $user->password = $password;
            $user->password = Hash::make($password);

            try {
                $user->save();
            } catch (\Exception $e) {
                $error = $e->deacMessage();
                $errorCode = $e->getCode();
            }

            if (!isset($error)) {
                return response()->json([
                    'message' => "Password Changed",
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => $error,
                ], 500);
            }
        }
    }

    public function checkMail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user->exists()) {
            return $this->sendCode($request->email);
        } else {
            return response()->json([
                'Message' => "User not found"
            ], 404);
        }
    }

    function sendCode($email)
    {
        $user =  User::where('email', $email)->first();

        if (VerificationCode::where('user_id', $user->id)->exists()) {
            VerificationCode::where('user_id', $user->id)->delete();
        }

        $code = new VerificationCode;
        $code->assignCode($user);

        Mail::to($user->email)->send(new SendResetCode($code->code()));

    }

    public function allUsers()
    {
        return response()->json(User::where('type', 'user')->with(['review', 'wallet', 'transaction', 'booking.products', 'referral'])->get());
    }

    public function allBusinessReps()
    {
        return response()->json(User::where('type', 'business')->with(['review', 'wallet', 'transaction', 'booking.products', 'referral'])->get());
    }

    public function allAdmins()
    {
        return response()->json(User::where('type', 'admin_one')->with(['review', 'wallet', 'transaction', 'booking.products', 'referral'])->get());
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth()->user();

        if (isset($request->first_name) and !empty($request->first_name)) {
            $user->first_name = $request->first_name;
        }
        if (isset($request->last_name) and !empty($request->last_name)) {
            $user->last_name = $request->last_name;
        }
        if (isset($request->mobile_number) and !empty($request->mobile_number)) {
            $user->mobile_number = $request->mobile_number;
        }
        if (isset($request->location) and !empty($request->location)) {
            $user->location = $request->location;
        }

        try {
            $user->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Profile Updated",
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updateDeviceDetails(Request $request)
    {
        $user = Auth()->user();

        $user->device_type = $request->device_type;
        $user->device_token = $request->device_token;

        try {
            $user->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Device Details Updated",
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function getUserById($id)
    {
        return response()->json([
            'data' => User::with(['review', 'wallet', 'transaction','vendorBooking.products', 'booking.products', 'referral'])->findOrFail($id)
        ], 200);
    }

    public function getAdmins()
    {
        $admins = User::where('type', 'admin_one')->get();

        return response()->json([
            'data' => $admins
        ],200);
    }
    
    public function getReps()
    {
        $admins = User::where('type', 'business')->with(['review', 'wallet', 'transaction', 'booking.products', 'referral'])->get();

        return response()->json([
            'data' => $admins
        ],200);
    }

    public function deactivateAccount($id){
        $user = User::findOrFail($id);
        $user->status = false;

        $user->save();

        return response()->json([
            'message' => 'Account disabled',
            'data' => $user
        ],200);
    }

    public function activateAccount($id){
        $user = User::findOrFail($id);
        $user->status = true;

        $user->save();

        return response()->json([
            'message' => 'Account activated',
            'data' => $user
        ],200);
    }

    function getwalletbyUserId($id){
        $user = User::findOrFail($id);

        $wallet = $user->wallet;

        return response()->json([
            'message' => 'wallet found',
            'data' => $wallet
        ],200);
    }

    public function testNotification($id)
    {

        $pendingNotification = [
            'title' => 'Hello From Whavit',
            'body' => 'We are here to give you the best cleaning experience.'
        ];
        
        $pendingData = [
            'title' => 'New User Notification',
            'message' => 'We are here to give you the best cleaning experience.',
            'action' => 'in_app',
            'action_destination' => 'No destination apart from the app'
        ];

        sendNotification($id,$pendingNotification,$pendingData);
    }

}
