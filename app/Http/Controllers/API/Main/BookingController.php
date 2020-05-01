<?php

namespace App\Http\Controllers\API\Main;

use DB;
use App\User;
use App\Extra;
use App\Notification;
use App\Traits\VendorUtil;
use App\Events\NewNotification;
use App\Booking;
use App\Discount;
use App\Service;
use App\Review;
use App\Task;
use App\Price;
use App\ServiceAttribute;
use App\AttributeName;
use App\Transaction;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\BookingSuccess;
use App\Mail\BookingCancelled;
use App\Mail\VendorRemoved;
use App\Mail\BookingRescheduled;
use App\Mail\BusinessBookingSuccess;
use Illuminate\Support\Facades\Mail;


class BookingController extends Controller
{
    use VendorUtil;

    public function updateAddress($bookingId, Request $request)
    {
        $this->validate($request, [
            'address' => 'required'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $booking->address = $request->address;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking address update",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updatePrice($bookingId, Request $request)
    {
        $this->validate($request, [
            'price' => 'required'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        // NEEDS ATTENTION
        $booking->net_price = $request->price;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking price update",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error, a
            ], 500);
        }
    }

    public function create(Request $request)
    {

        $this->validate($request, []);

        $booking = new Booking;

        try {
            Auth()->user()->booking()->save($booking);
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking created",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function showAll()
    {
        return response()->json([
            'message' => 'Ok',
            'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'serviceAttribute', 'vendor', 'task'])->latest()->get()
        ]);
    }


    public function showByUser()
    {
        $booking = Auth()->user()->booking()->with(['extra', 'products.area', 'user', 'service','serviceAttribute', 'vendor', 'task'])->where('status','!=','canceled')->latest()->get();
        
        return response()->json([
            'message' => 'Ok',
            'data' => $booking
        ]);
    }

    public function showOne($id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'serviceAttribute', 'vendor', 'task'])->findOrFail($id);

        return response()->json([
            'message' => 'Ok',
            'data' => $booking
        ], 200);
    }
    
    public function vendorDetails($vendor_id)
    {
        // Get rating and pass to payload
        $raw_rating = Review::where('user_id', $vendor_id)->avg('rating');
        $formated_rating = number_format((float) $raw_rating, 1, '.', '');

        //Get accepted jobs by vendor and pass to payload
        $vendorDetails = DB::table('booking_vendor')->where('user_id', $vendor_id)->get();

        return response()->json([
            'message' => 'Ok',
            'rating' => $formated_rating,
            'no_of_jobs' => count($vendorDetails)
        ], 200);
    }

    public function updateCategory($bookingId, $id)
    {

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $productCategory = ProductCategory::findOrFail($id);
        try {
            if (count($booking->products) > 0) {
                foreach ($booking->products as $product) {
                    $id = $product->id;
                    $pro = ProductCategory::find($id);
                    $price = $productCategory->price()->first();
                    $booking->price()->detach($price);
                    $booking->products()->detach($pro);
                    $booking->decrement('base_price', $productCategory->getPrice());
                }
            }
            $price = $productCategory->price()->first();
            $booking->increment('base_price', $productCategory->getPrice());
            $booking->products()->attach($productCategory);
            $booking->price()->attach($price);
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updateSchedule(Request $request, $bookingId)
    {
        $this->validate($request, [
            'schedule' => 'required|in:monthly,bi-monthly,bi-weekly,weekly:once'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);
        $discount = 0;
        switch ($request->schedule) {
            case 'once':
                $discount = 0;
                break;
            case 'monthly':
                $discount = 0.05;
                break;
            case 'bi-monthly':
                $discount = 0.075;
                break;
            case 'weekly':
                $discount = 0.10;
                break;
            case 'bi-weekly':
                $discount = 0.125;
                break;
            default:
                $discount = 0;
        }

        try {

            $booking->update([
                'schedule' => $request->schedule,
                'discount' => $discount
            ]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking Schedule updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updateTime(Request $request, $bookingId)
    {
        $this->validate($request, [
            'time' => 'required'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $time = $request->time;
        $booking->time = $time;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking Time updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updateStartDate(Request $request, $bookingId)
    {
        $this->validate($request, [
            'start_date' => 'required|date|after:today'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $start_date = $request->start_date;
        $booking->start_date = $start_date;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "start date updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updateLocation(Request $request, $bookingId)
    {
        $this->validate($request, [
            'location' => 'required'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $location = $request->location;
        $booking->location = $location;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Location updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }


    public function updateRooms(Request $request, $bookingId)
    {
        $this->validate($request, [
            'no_of_rooms' => 'required|integer|max:10'
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $no_of_rooms = $request->no_of_rooms;
        $booking->no_of_rooms = $no_of_rooms;

        try {
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking number of rooms updated",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function rescheduleBooking(Request $request, $bookingId)
    {
        $this->validate($request, [
            'schedule' => 'required',
            'time' => 'required',
            'location' => 'required',
            'address' => 'required',
            'start_date' => 'required|date|after:today',
        ]);

        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);
        $discount = 0;
        switch ($request->schedule) {
            case 'once':
                $discount = 0;
                break;
            case 'monthly':
                $discount = 0.05;
                break;
            case 'bi-monthly':
                $discount = 0.075;
                break;
            case 'weekly':
                $discount = 0.10;
                break;
            case 'bi-weekly':
                $discount = 0.125;
                break;
            default:
                $discount = 0;
        }

        try {

            $booking->schedule = $request->schedule;
            $booking->discount = $discount;
            $booking->time = $request->time;
            $booking->location = $request->location;
            $booking->address = $request->address;
            $booking->start_date = $request->start_date;
            $booking->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BookingRescheduled(Auth()->user()));

            $pendingNotification = [
                'title' => 'Cleaning Rescheduled',
                'body' => 'Your cleaning has been rescheduled.'
            ];

            $pendingData = [
                'title' => 'Cleaning Rescheduled',
                'message' => 'Your cleaning has been rescheduled.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];

            sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);

            return response()->json([
                'message' => "Booking Rescheduled",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function cancelRequest($bookingId)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        // return response()->json(,200);
        $booking->status = 'canceled';

        $vendors = $booking->vendor->toArray();

        foreach ($vendors as $vendor) {
            $user =  User::find($vendor['id']);
            $booking->vendor()->detach($vendor);
            $user->booking_status = false;
            $user->save();
            Mail::to([$user->email, 'hello@whavit.com'])->send(new VendorRemoved($user));
        }

        try {
            $booking->save();
            // Refund wallet here
            $booking_price = $booking->net_price;
            $user = Auth()->user();
            $wallet = $user->wallet;
            $wallet->increment('balance', $booking_price);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BookingCancelled(Auth()->user()));

            $pendingNotification = [
                'title' => 'Cleaning Canceled',
                'body' => 'We are sad to see you cancel your request. Our Representative will be in touch to know your concerns.'
            ];

            $pendingData = [
                'title' => 'Cleaning Canceled',
                'message' => 'We are sad to see you cancel your request. Our Representative will be in touch to know your concerns.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];

            sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);

            return response()->json([
                'message' => "Booking request Canceled",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function createFullBooking(Request $request)
    {
        $this->validate($request, [
            'base_price' => 'required',
            'no_of_rooms' => 'required',
            'schedule' => 'required',
            'time' => 'required',
            'location' => 'required',
            'address' => 'required',
            'start_date' => 'required',
        ]);

        $booking = new Booking;
        $booking->save();

        //Save product Category
        $productCategory = null;
        $price = null;
        if ($request->product !== null) {
            $productCategory = ProductCategory::findOrFail($request->product);
            $price = $productCategory->price()->first();
            // $booking->increment('base_price',$productCategory->getPrice());
            $booking->products()->attach($productCategory);
        }

        $booking->price()->attach($price);

        if ($request->booking_extra) {
            $extra_array = explode(",", $request->booking_extra);
            foreach ($extra_array as $id) {
                $extra = Extra::findOrFail($id);
                $booking->extra()->attach($extra);
            }
        }

        if ($request->booking_service_attributes) {
            foreach ($request->booking_service_attributes as $singleServiceAttribute) {
                $serviceAttributes = ServiceAttribute::findOrFail($singleServiceAttribute[0]);
                $serviceAttributes->service_base_amount = $singleServiceAttribute[1];

                // dd($serviceAttributes);
                $booking->serviceAttribute()->attach($serviceAttributes, ['service_base_amount' => $singleServiceAttribute[1]]);
            }
        }

        if ($request->booking_service) {
            $service_array = explode(",", $request->booking_service);
            foreach ($service_array as $id) {
                $service = Service::findOrFail($id);
                $booking->service()->attach($service);
            }
        }

        // update no_of_rooms, start_date, location, schedule, and time
        $booking->booking_type = Auth()->user()->type;
        $booking->no_of_rooms = $request->no_of_rooms;
        $booking->schedule = $request->schedule;
        $booking->location = $request->location;
        $booking->start_date = $request->start_date;
        $booking->time = $request->time;
        $booking->address = $request->address;
        $booking->base_price = $request->base_price;
        $booking->net_price = $request->net_price;

        try {
            $booking->save();
            Auth()->user()->booking()->save($booking);
            if (Auth()->user()->type == "business") {
                Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BusinessBookingSuccess(Auth()->user()));
                $pendingNotification = [
                    'title' => 'Cleaning Booked',
                    'body' => 'Thanks for making a cleaning request. We will be in touch in less than 12 hours.'
                ];

                $pendingData = [
                    'title' => 'Cleaning Booked',
                    'message' => 'Thanks for making a cleaning request. We will be in touch in less than 12 hours.',
                    'action' => 'in_app',
                    'action_destination' => 'No destination apart from the app'
                ];

                sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);
            } else {

                Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BookingSuccess(Auth()->user()));

                $pendingNotification = [
                    'title' => 'Cleaning Booked',
                    'body' => 'Your cleaning is booked. Please do leave a review for your WhavPro after cleaning so we can serve you better.'
                ];

                $pendingData = [
                    'title' => 'Cleaning Booked',
                    'message' => 'Your cleaning is booked. Please do leave a review for your WhavPro after cleaning so we can serve you better.',
                    'action' => 'in_app',
                    'action_destination' => 'No destination apart from the app'
                ];

                sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);
                $this->sendAvailableVendorRequest($booking->start_date);
            }
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking created",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }


    public function createSpecialBooking(Request $request)
    {
        $this->validate($request, [
            'base_price' => 'required',
            'schedule' => 'required',
            'time' => 'required',
            'location' => 'required',
            'address' => 'required',
            'start_date' => 'required',
        ]);

        $booking = new Booking;
        $booking->save();

        if ($request->booking_extra) {
            $extra_array = explode(",", $request->booking_extra);
            foreach ($extra_array as $id) {
                $extra = Extra::findOrFail($id);
                $booking->extra()->attach($extra);
            }
        }

        if ($request->booking_service_attributes) {
            foreach ($request->booking_service_attributes as $singleServiceAttribute) {
                $serviceAttributes = ServiceAttribute::findOrFail($singleServiceAttribute[0]);
                $serviceAttributes->service_base_amount = $singleServiceAttribute[1];
                $booking->serviceAttribute()->attach($serviceAttributes, ['service_base_amount' => $singleServiceAttribute[1]]);
            }
        }

        if ($request->booking_service) {
            $service_array = explode(",", $request->booking_service);
            foreach ($service_array as $id) {
                $service = Service::findOrFail($id);
                $booking->service()->attach($service);
            }
        }

        // update no_of_rooms, start_date, location, schedule, and time
        $booking->booking_type = Auth()->user()->type;
        $booking->schedule = $request->schedule;
        $booking->no_of_rooms = $request->no_of_rooms;
        $booking->location = $request->location;
        $booking->start_date = $request->start_date;
        $booking->time = $request->time;
        $booking->address = $request->address;
        $booking->base_price = $request->base_price;
        $booking->net_price = $request->net_price;

        try {
            $booking->save();
            Auth()->user()->booking()->save($booking);
            if (Auth()->user()->type == "business") {
                Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BusinessBookingSuccess(Auth()->user()));
                $pendingNotification = [
                    'title' => 'Cleaning Booked',
                    'body' => 'Thanks for making a cleaning request. We will be in touch in less than 12 hours.'
                ];

                $pendingData = [
                    'title' => 'Cleaning Booked',
                    'message' => 'Thanks for making a cleaning request. We will be in touch in less than 12 hours.',
                    'action' => 'in_app',
                    'action_destination' => 'No destination apart from the app'
                ];

                sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);
            } else {
                Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BookingSuccess(Auth()->user()));

                $pendingNotification = [
                    'title' => 'Cleaning Booked',
                    'body' => 'Your cleaning is booked. Please do leave a review for your WhavPro after cleaning so we can serve you better.'
                ];

                $pendingData = [
                    'title' => 'Cleaning Booked',
                    'message' => 'Your cleaning is booked. Please do leave a review for your WhavPro after cleaning so we can serve you better.',
                    'action' => 'in_app',
                    'action_destination' => 'No destination apart from the app'
                ];

                sendNotification(Auth()->user()->id, $pendingNotification, $pendingData);
                $this->sendAvailableVendorRequest($booking->start_date);
            }
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking created",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }



    public function newRequestNotf($vendor)
    {
        $notification = new Notification();
        $notification->type = 'request';
        $notification->content = 'You have a new request';

        $vendor = $vendor;
        $vendor->notification()->save($notification);

        return event(new NewNotification($notification));
    }

    public function newRequestNotfTest($id)
    {
        $notification = new Notification();
        $notification->type = 'request';
        $notification->content = 'You have a new request';

        $vendor = User::findOrFail($id);
        $vendor->notification()->save($notification);

        event(new NewNotification($notification));

        return 'event sent';
    }

    public function showBooking($id)
    {
        $booking = Booking::find($id);

        $user = User::find($booking->user_id);

        //Get Product
        $product = DB::table('product_booking')->where('booking_id', $booking->id)->first();
        $productCategory = ProductCategory::findOrFail($product->product_id);

        // Get Booking Extras
        $bookingExtras = DB::table('booking_extra')->where('booking_id', $booking->id)->get();

        // Get Booking Extras Attributes
        $bookingExtrasAttributes = DB::table('booking_extra_attribute')->where('booking_id', $booking->id)->get();

        // Get Services
        $bookingServices = DB::table('booking_service')->where('booking_id', $booking->id)->get();

        return compact('user', 'booking', 'productCategory', 'bookingExtras', 'bookingExtrasAttributes', 'bookingServices');
    }

    public function updateBooking(Request $request, $id)
    {
        $this->validate($request, [
            'net_price' => 'required',
            'no_of_rooms' => 'required',
            'schedule' => 'required',
            'time' => 'required',
            'location' => 'required',
            'address' => 'required',
            'start_date' => 'required|date|after:today',
        ]);

        $booking = Booking::find($id);

        if ($request->booking_extra) {
            $extra_array = explode(",", $request->booking_extra);
            foreach ($extra_array as $id) {
                $booked_extra = DB::table('booking_extra')->where('extra_id', $id)->where('booking_id', $booking->id)->first();
                if ($booked_extra !== null) {
                    continue;
                }
                $extra = Extra::findOrFail($id);
                $booking->extra()->attach($extra);
            }
        }

        if ($request->booking_extra_attributes) {
            $extra_attributes_array = explode(",", $request->booking_extra_attributes);
            foreach ($extra_attributes_array as $id) {
                $booked_extra_attribute = DB::table('booking_extra_attribute')->where('extra_attribute_id', $id)->where('booking_id', $booking->id)->first();
                if ($booked_extra_attribute !== null) {
                    continue;
                }
                $extraAttributes = ExtraAttribute::findOrFail($id);
                $booking->extraAttribute()->attach($extraAttributes);
            }
        }

        if ($request->booking_service) {
            $service_array = explode(",", $request->booking_service);
            foreach ($service_array as $id) {
                $booked_service = DB::table('booking_service')->where('service_id', $id)->where('booking_id', $booking->id)->first();
                if ($booked_service !== null) {
                    continue;
                }
                $service = Service::findOrFail($id);
                $booking->service()->attach($service);
            }
        }

        // update no_of_rooms, start_date, location, schedule, and time
        $booking->no_of_rooms = $request->no_of_rooms;
        $booking->schedule = $request->schedule;
        $booking->location = $request->location;
        $booking->start_date = $request->start_date;
        $booking->time = $request->time;
        $booking->address = $request->address;
        $booking->net_price = $request->net_price;

        try {
            $booking->save();
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Booking updated",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
