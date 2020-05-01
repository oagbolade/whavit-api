<?php

namespace App\Http\Controllers\API\Auth;

use Auth;
use  App\User;
use Socialite;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
// use Mockery\Exception;
use App\Http\Requests\ChangePasswordRequest;
use App\VerificationCode;
use App\Wallet;
use App\Mail\UserRegistered;
use App\Mail\VendorRegistered;
use App\Mail\BusinessRegistered;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function authenticate(Request $request)
    {
        //Validate fields
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        //Attempt validation
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->setTTl(800000)->attempt($credentials)) {
            return response()->json(['error' => 'Incorrect credentials'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => Auth()->user()
        ]);
    }


    public function updateProfilePicture(Request $request)
    {
        $this->validate($request, [
            'img_url' => 'required'
        ]);

        $user = Auth()->user();

        $user->img_url = $request->img_url;


        try {
            $user->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Profile picture updated",
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    public function register(Request $request){
        //Validate fields
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:users',
            'first_name' => 'required|max:255',
            'last_name' => 'max:255',
            'type' => 'in:user,admin_one,admin_two,business,vendor',
            'password' => 'required|min:8',
            'mobile_number' => ''
        ]);
        //Create user, generate token and return

        if (isset($request->referral_code)) {
            $ref_code = $request->referral_code;
            $referee = User::where('referral_code', $ref_code)->first();
            $referee_id = $referee->id;
            
            $user = new User;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->type = $request->type;
            $user->email = $request->email;
            $user->password = Hash::make($request->input('password'));
            $user->referral_code = str_random(6);
            $user->referred_by_id = $referee_id;
            $user->mobile_number = $request->mobile_number;
            $user->business_name = $request->business_name;
            
            $user->save();
            $token = JWTAuth::customClaims([$user]);
            Mail::to($request->email)->send(new UserRegistered($user));
            return response()->json(compact('token'));
        } else {
            $user =  User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'type' => $request->input('type'),
                'password' => Hash::make($request->input('password')),
                'referral_code' => str_random(6),
                'mobile_number' => $request->input('mobile_number'),
                'business_name' => $request->input('business_name')
            ]);

            $wallet = new Wallet;

            $user->wallet()->save($wallet);
            // $code = new VerificationCode($user);
            // $token = JWTAuth::fromUser($user);
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            $token = auth()->setTTl(800000)->attempt($credentials);

            //Send Mail
            if ($request->input('type') == "business") {

                Mail::to($request->input('email'))->send(new BusinessRegistered($user));

            } elseif($request->input('type') == "vendor") {

                Mail::to($request->input('email'))->send(new VendorRegistered($user));

            } else {
                Mail::to($request->input('email'))->send(new UserRegistered($user));
            }
            
            return response()->json([
                'token' => $token,
                'user' => User::find($user->id)
            ]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {

        $user = Auth()->user();
        $old_password = $user->password;

        if (Hash::check($request->old_password, $old_password)) {
            $user->password = bcrypt($request->password);

            try {
                $user->save();
            } catch (\Exception $e) {
                $error = $e->getMessage();
                $errorCode = $e->getCode();
            }

            if (!isset($error)) {
                return response()->json([
                    'message' => "Password changed",
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => $error,
                ], 500);
            }
        } else {
            return response()->json([
                "error" => "Old password is incorrect",
            ], 422);
        }
    }
}
