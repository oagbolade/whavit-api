<?php

namespace App\Http\Controllers\API\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\DisinfectionMailable;

class DisinfectionResponderController extends Controller
{
    public function disinfectionResponder(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'message' => 'required',
            'square_metres' => 'required',
            'square_metres_amount' => 'required',
        ]);

        $name = $request->name;
        $email = $request->email;
        $phone = $request->phone;
        $message = $request->message;
        $company_name = $request->company_name;
        if ($request->company_name === null) {
            $company_name = "Not Specified";
        }
        $square_metres = $request->square_metres;
        $square_metres_amount = $request->square_metres_amount;

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'company_name' => $company_name,
            'square_metres' => $square_metres,
            'square_metres_amount' => $square_metres_amount,
        ];

        Mail::to($email)->send(new DisinfectionMailable($data, true));

        $whavit_email = 'hello@whavit.com';
        Mail::to($whavit_email)->send(new DisinfectionMailable($data, false));

        return response()->json([
            'status' => 200,
            'message' => "Mail Sent Successfully",
            'data' =>
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'company_name' => $company_name,
                'square_metres' => $square_metres,
                'square_metres_amount' => $square_metres_amount,
            ]
        ], 200);
    }
}
