<?php

namespace App\Http\Controllers\API\Main;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\WhavPremiumMailable;

class WhavPremiumResponderController extends Controller
{
    public function whavPremiumResponder(Request $request)
    {
        
        $name = $request->name;
        $email = $request->email;
        $type = $request->type;
        $phone = $request->phone;
        $message = $request->message;

        $data = [
            'name' => $name,
            'email' => $email,
            'type' => $type,
            'phone' => $phone,
            'message' => $message,
        ];
        
        Mail::to($email)->send(new WhavPremiumMailable($type, $data, true));

        $whavit_email = 'hello@whavit.com';
        Mail::to($whavit_email)->send(new WhavPremiumMailable($type, $data, false));
        
        return response()->json([
            'status' => 200,
            'message' => "Mail Sent Successfully",
            'data' => 
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ]
        ], 200);
    }

}