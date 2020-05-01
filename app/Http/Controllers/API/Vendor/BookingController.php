<?php

namespace App\Http\Controllers\API\Vendor;

use DB;
use App\User;
use App\Booking;
use App\Traits\VendorUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    use VendorUtil;

    public function showBookings(Request $request)
    {
        // $bookings = Booking::with(['extra','products','user'])->where('assigned_vendor',Auth()->user()->id)->get();
        // $bookings = Auth()->user()->vendorBooking()->with(['extra', 'products.area', 'user', 'service'])->get();
        $vendorId = $request->user_id;
        $bookings = [];
        $allbookings = Booking::with(['extra', 'products.area', 'user', 'service', 'serviceAttribute', 'vendor', 'task'])->get();

        //Here I want to get all bookings and also filter bookings by business so                         
        foreach($allbookings as $booking) {
            if($booking->booking_type == 'business' && $this->checkIfVendorIsAssigned($booking->id,$vendorId) == false) {
                continue;
            }
            array_push($bookings,$booking);
        }
        return response()->json([
            'data' => $bookings
        ], 200);
    }
    
    public function showCompletedBookings($vendorId)
    {
        $bookings = [];
        $allbookings = Booking::where(['status' => 'completed', 'completed_by' => $vendorId])->get();

        //Here I want to get all bookings and also filter bookings by business so                         
        foreach($allbookings as $booking) {
            if($booking->booking_type == 'business') {
                continue;
            }
            array_push($bookings,$booking);
        }

        return response()->json([
            'data' => $bookings
        ], 200);
    }

    public function showAcceptedBookings($id)
    {
        $vendorId = $id;
        $bookings = [];
        $allbookings = DB::table('booking_vendor')->select('*')->where('user_id', '=', $vendorId)->get();

        //Here I want to get all bookings and also filter bookings by business so                         
        foreach ($allbookings as $booking) {
            array_push($bookings,Booking::with(['extra', 'products.area', 'user', 'service', 'serviceAttribute', 'vendor', 'task'])->find($booking->booking_id));

        }
        return response()->json([
            'data' => $bookings
        ], 200);
    }

    public function showRejectedBookings()
    {
        $allbookings = DB::table('bookings')->select('*')->where(['status' => 'rejected'])->get();
        
        return response()->json([
            'data' => $allbookings
        ], 200);
    }

    public function showPendingBookings(Request $request)
    {
        $vendorId = $request->user_id;
        $bookings = [];
        $allbookings = Booking::with(['extra', 'products.area', 'user', 'service', 'serviceAttribute', 'vendor', 'task'])->where(['status' => 'pending'])->get();

        //Here I want to get all bookings and also filter bookings by business so     
        foreach ($allbookings as $booking) {
            if ($booking->booking_type == 'business' && $this->checkIfVendorIsAssigned($booking->id, $vendorId) == false) {
                continue;
            }
            array_push($bookings, $booking);
        }
        return response()->json([
            'data' => $bookings
        ], 200);
    }

    public function findNearest(Request $request)
    {
        $this->validate($request, [
            'location' => 'required'
        ]);

        $vendors = User::notBooked()->where('location', $request->location)->get();

        return response()->json([
            'message' => 'Unbooked vendors within the location selected',
            'data' => $vendors
        ], 200);
    }

    public function getOnlineByLocation($location)
    {
        $vendors = User::vendor()->online()->located($location)->take(2)->get();

        $other_vendors = null;
        switch (count($vendors)) {
            case 0:
                $other_vendors = User::vendor()->online()->take(2)->get();
                break;

            case 1:
                $other_vendors = User::vendor()->online()->take(1)->get();
                break;

            default:
                $other_vendors = null;
                break;
        }

        return response()->json([
            'message' => 'vendors found',
            'data' => $vendors,
            'others' => $other_vendors
        ],200);
    }
}
