<?php

namespace App\Http\Controllers\API\Vendor;

use DB;
use App\User;
use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Location;
use GuzzleHttp\Client;
use App\Traits\VendorUtil;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use App\Bank;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    use VendorUtil;

    public function acceptRequest($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->status = 'accepted';

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error
            ], 500);
        }
    }

    public function rejectRequest($id)
    {
        $vendor = Auth()->user();
        $booking = Booking::findOrFail($id);

        $booking->status = 'rejected';

        try {
            $vendor->booking_status = false;
            $vendor->save();
            $booking->vendor()->detach($vendor);
            $booking->save();
            $this->sendAvailableVendorRequest($booking->start_date);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error
            ], 500);
        }
    }

    public function showAll()
    {
        return response()->json([
            "message" => "vendors fetched",
            "data" => User::where('type', 'vendor')->with(['vendorBooking', 'review.user'])->get()
        ], 200);
    }

    public function showUnbooked(Request $request)
    {
        $start_date = $request->start_date;
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
        return response()->json([
            "message" => "vendors fetched",
            "data" => $unbookedVendors
        ], 200);
    }

    public function showOne($id)
    {
        $vendor = User::with(['vendorBooking', 'review.user'])->findOrFail($id);

        if ($vendor->isVendor()) {
            return response()->json([
                "message" => "vendor found",
                "data" => $vendor
            ], 200);
        } else {
            return response()->json([
                'message' => 'Selected user is not a Vendor'
            ], 404);
        }
    }

    public function setOffline()
    {
        $vendor = Auth()->user();
        $vendor->availability = false;

        try {
            Auth()->user()->save();
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Vendor offline",
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function setOnline()
    {
        $vendor = Auth()->user();
        $vendor->availability = true;

        try {
            Auth()->user()->save();
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Vendor online",
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    
    public function getOnlineVendors(){
        $vendors = User::where(['type' => 'vendor'])->get();
        $online_vendors = [];
        
        foreach ($vendors as $vendor) {
            if (Cache::has($vendor->id)) {
                $value = Cache::get($vendor->id);
                array_push($online_vendors, $vendor);
            }
        }

        if(count($online_vendors) > 0){
            return response()->json([
                'vendors' => $online_vendors,
                'found' => true,
                'number_online' => count($online_vendors)
            ], 200);
        }else{
            return response()->json([
                'message' => 'Vendors are offline',
                'found' => false
            ], 500);
        }
    }
}
