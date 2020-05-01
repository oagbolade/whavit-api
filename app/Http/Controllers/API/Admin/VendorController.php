<?php

namespace App\Http\Controllers\API\Admin;

use DB;
use Carbon\Carbon;
use App\User;
use App\VendorTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VendorTrainingMail;
use Illuminate\Support\Facades\Mail;

class VendorController extends Controller
{
    public function acceptVendor($id)
    {
        $vendor = User::findOrFail($id);

        if(!$vendor->isVendor()){
            return response()->json([
                'message' => "selected user is not a vendor",
            ],400);
        }
        $vendor->status = true;

        try {
            $vendor->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Vendor Accepted",
                'data' => $vendor
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    
    public function rejectVendor($id)
    {
        $vendor = User::findOrFail($id);

        $vendor->status = false;

        try {
            $vendor->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Vendor Rejected",
                'data' => $vendor
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function verifyVendor($id)
    {
        $vendor = User::findOrFail($id);

        if(!$vendor->isVendor()){
            return response()->json([
                'message' => "selected user is not a vendor",
            ],400);
        }
        $vendor->verified = true;

        try {
            $vendor->save();
            $this->scheduleTraining($vendor);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Vendor Verified",
                'data' => $vendor
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    function scheduleTraining($vendor)
    {
        $thisdate = Carbon::parse('this Saturday')->toDateString();
        $nextdate = Carbon::parse('next Saturday')->toDateString();

        //Get total people assigned for training
        $trainees = VendorTraining::where('training_date',$thisdate)->get();

        if (count($trainees) == 25) {
            //Move to next date
            $date = strtotime("+7 day");
            $next_saturday = date($thisdate, $date);
            $training = VendorTraining::create(
                [
                    'user_id' => $vendor->id,
                    'training_date' => $next_saturday,
                    'training_time' => '9:00 AM'
                ]
            );
        } else {
            $training = VendorTraining::create(
                [
                    'user_id' => $vendor->id,
                    'training_date' => $thisdate,
                    'training_time' => '9:00 AM'
                ]
            );
        }

        Mail::to([$vendor->email, 'hello@whavit.com'])->send(new VendorTrainingMail($vendor, $training));

        return true;
   }
}
