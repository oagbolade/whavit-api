<?php

namespace App\Traits;

use DB;
use App\User;
use App\Booking;
use App\Mail\VendorHired;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

trait VendorUtil
{
    function getUnbooked($start_date)
    {
        $unbookedVendors = [];
        $vendors = User::notBooked()->where('type', 'vendor')->where('status', true)->with(['vendorBooking', 'review.user'])->get();
    
        foreach($vendors as $vendor) {
            $vendorBooking = DB::table('booking_vendor')->select('*')->where('cleaning_date','=',$start_date)->where('user_id','=',$vendor->id)->first();
            if($vendorBooking) {
                continue;
            }else {
                array_push($unbookedVendors,User::find($vendor->id));
            }
        }

        return  $unbookedVendors;
    } 

    function sendAvailableVendorRequest($start_date)
    {
        $vendors = $this->getUnbooked($start_date);

        foreach($vendors as $vendor) {
            // Mail::to([$vendor->email, 'hello@whavit.com'])->send(new VendorHired($vendor));

            $pendingNotification = [
                'title' => 'New Request',
                'body' => 'There is a new cleaning request by a Whavit User. Quickly Accept Before Other WhavPro Do!'
            ];
            
            $pendingData = [
                'title' => 'New Request',
                'message' => 'There is a new cleaning request by a Whavit User. Quickly Accept Before Other WhavPro Do!',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification($vendor->id,$pendingNotification,$pendingData); 
        }

        return true;
        
    }

    function checkIfVendorIsAssigned($bookingId,$vendorId)
    {
        $vendorBooking = DB::table('booking_vendor')->select('*')->where('booking_id','=',$bookingId)->where('user_id','=',$vendorId)->first();

        if(!$vendorBooking) {
            return false;
        }

        return true;

    }

    function checkIfVendorExistsForBooking($bookingId)
    {
        $vendorBooking = DB::table('booking_vendor')->select('*')->where('booking_id','=',$bookingId)->first();

        if(!$vendorBooking) {
            return false;
        }

        return true;

    }
}