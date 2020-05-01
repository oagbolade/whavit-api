<?php

namespace App\Http\Controllers\API\Main;

use App\User;
use DB;
use App\Task;
use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VendorHired;
use App\Mail\VendorRemoved;
use App\Mail\VendorAccepted;
use App\Mail\VendorRejected;
use App\Traits\VendorUtil;
use App\Review;
use Illuminate\Support\Facades\Mail;

class VendorController extends Controller
{
    use VendorUtil;

    public function vendorAccept($bookingId)
    {   
        $vendor = Auth()->user();

        $booking = Booking::findOrFail($bookingId);

        //Check if user that booked is a business, if business then allow more than one vendor accept
        if($booking->booking_type == 'business' && $this->checkIfVendorIsAssigned($bookingId,$vendor->id)  == true) {
            $booking->vendor()->attach($vendor,['cleaning_date' => $booking->start_date]);
            $booking->status = 'accepted';
            $vendor->booking_status = 0;
            $vendor->save();
        }

        if($booking->booking_type == 'user' && $this->checkIfVendorExistsForBooking($bookingId) == false) {
            $booking->vendor()->attach($vendor,['cleaning_date' => $booking->start_date]);
            $booking->status = 'accepted';
            $vendor->booking_status = 0;
            $vendor->save();
        }
        
        if (isset($booking->products[0]->area) and count($booking->products[0]->area) > 0) {
            foreach ($booking->products[0]->area as $this_task) {
                $task = new Task;
                $task->task = $this_task->title;

                $booking->task()->save($task);
            }
        }
        if (isset($booking->service) and count($booking->service) > 0) {
            foreach ($booking->service as $this_task) {
                $task = new Task;
                $task->task = $this_task->title;

                $booking->task()->save($task);
            }
        }

        if (isset($booking->extra) and count($booking->extra) > 0) {
            foreach ($booking->extra as $this_task) {
                $task = new Task;
                $task->task = $this_task->title;

                $booking->task()->save($task);
            }
        }

        try {
            $booking->save();
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            $id = Auth()->user()->id;

            // Get completed requests and pass count to payload
            $bookings = [];
            $allbookings = Booking::where(['status' => 'completed', 'completed_by' => $id])->get();

            //Here I want to get all bookings and also filter bookings by business so                         
            foreach ($allbookings as $booking) {
                if ($booking->booking_type == 'business') {
                    continue;
                }
                array_push($bookings, $booking);
            }

            // Get rating and pass to payload
            $raw_rating = Review::where('user_id', $id)->avg('rating');
            $formated_rating = number_format((float) $raw_rating, 1, '.', '');

            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new VendorAccepted(Auth()->user()));
            $pendingNotification = [
                'title' => 'You accepted a request.',
                'body' => 'Be sure to deliver your services like a Whavpro also, ensure you are in your complete outfit before going for the cleaning.'
            ];
            
            $pendingData = [
                'title' => 'You accepted a request.',
                'message' => 'Be sure to deliver your services like a Whavpro also, ensure you are in your complete outfit before going for the cleaning.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app',
                'vendor_rating' => $formated_rating,
                'no_of_completed_requests' => count($bookings)
            ];
    
            sendNotification(Auth()->user()->id,$pendingNotification,$pendingData);
            return response()->json([
                'message' => "Booking request accepted",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId),
                'vendor_rating' => $formated_rating,
                'no_of_completed_requests' => count($bookings)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function vendorReject($bookingId)
    {
        $vendor = Auth()->user();

        $booking = Booking::findOrFail($bookingId);
        $booking->status = 'rejected';
        $vendor->booking_status = 0;
        $booking->vendor()->detach($vendor);

        try {
            $vendor->save();
            $booking->save();
            $this->sendAvailableVendorRequest($booking->start_date);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new VendorRejected(Auth()->user()));

            $pendingNotification = [
                'title' => 'WhavPro Declined Your Request',
                'body' => 'Your assigned Whavpro has rejected your request. Another WhavPro has been assigned'
            ];
            
            $pendingData = [
                'title' => 'WhavPro Declined Your Request',
                'message' => 'Your assigned Whavpro has rejected your request. Another WhavPro has been assigned',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification(Auth()->user()->id,$pendingNotification,$pendingData);

            return response()->json([
                'message' => "Booking request rejected",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function assignVendor(Request $request, $bookingId, $id)
    {
        $this->validate($request, [
            'price' => 'integer'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        if (isset($request->price)) {
            $booking->base_price = $request->price;
        }

        $vendor = User::findOrFail($id);
        
        // $booking->assigned_vendor =  $id;
        try {
            // $booking->save();
            // $this->newRequestNotf($vendor);
            $booking->vendor()->attach($vendor,['cleaning_date' => $booking->start_date]);
            $vendor->booking_status = 0;
            $vendor->save();
            $booking->save();
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        
        if (!isset($error)) {
            Mail::to([$vendor->email, 'hello@whavit.com'])->send(new VendorHired($vendor));

            $pendingNotification = [
                'title' => 'New Request',
                'body' => 'You have been hired for a cleaning request by a Whavit User.'
            ];
            
            $pendingData = [
                'title' => 'New Request',
                'message' => 'You have been hired for a cleaning request by a Whavit User.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification($vendor->id,$pendingNotification,$pendingData);

            return response()->json([
                'message' => "Vendor Assigned",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function unAssignVendor($bookingId, $id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $vendor = User::findOrFail($id);
        // $booking->assigned_vendor =  $id;
        try {
            // $booking->save();
            $booking->vendor()->detach($vendor);
            $vendor->booking_status = 0;
            $vendor->save();
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            Mail::to([$vendor->email, 'hello@whavit.com'])->send(new VendorRemoved($vendor));

            $pendingNotification = [
                'title' => 'Request Cancelled',
                'body' => 'You have been removed from a cleaning request.'
            ];
            
            $pendingData = [
                'title' => 'Request Cancelled',
                'message' => 'You have been removed from a cleaning request.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification($vendor->id,$pendingNotification,$pendingData);

            return response()->json([
                'message' => "Vendor removed",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function assignManyVendors(Request $request, $bookingId, $startdate)
    {
        $startdate = str_replace('-', '/', $startdate);
        
        $this->validate($request, [
            'ids' => 'required',
        ]);

        $arrayOfIds = explode(",", $request->ids);
        $booking = Booking::findOrFail($bookingId);

        $pendingNotification = [
            'title' => 'New Request',
            'body' => 'You have been hired for a cleaning request by a Whavit User.'
        ];
        
        $pendingData = [
            'title' => 'New Request',
            'message' => 'You have been hired for a cleaning request by a Whavit User.',
            'action' => 'in_app',
            'action_destination' => 'No destination apart from the app'
        ];

        foreach ($arrayOfIds as $id) {
            $vendor = User::findOrFail($id);

            $booking->vendor()->attach($vendor, ['cleaning_date' => $startdate]);
            $booking->status = "accepted";
            $booking->save();
            $vendor->booking_status = 1;
            $vendor->save();
            Mail::to([$vendor->email, 'hello@whavit.com'])->send(new VendorHired($vendor));
            sendNotification($vendor->id,$pendingNotification,$pendingData);
        }

        return response()->json([
            "message" => "Vendors assigned",
            "data" => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
        ], 200);
    }
}
