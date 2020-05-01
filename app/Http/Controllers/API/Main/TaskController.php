<?php

namespace App\Http\Controllers\API\Main;

use DB;
use App\Task;
use App\User;
use App\Extra;
use App\Booking;
use App\Discount;
use App\Wallet;
use App\Service;
use App\Price;
use App\ExtraAttribute;
use App\AttributeName;
use App\Transaction;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NextBookingScheduled;
use App\Mail\VendorWalletFunded;
use App\Mail\BookingCompleted;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function taskDone($task_id)
    {
        $task = Task::findOrFail($task_id);

        $task->status = true;

        try {
            $task->save();
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Task done",
                'data' => $task
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function allDone($bookingId)
    {
        $booking = Booking::where('id',$bookingId)->with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->first();
        $get_vendor_id = Booking::with(['vendor'])->findOrFail($bookingId);
        $vendor_id = $get_vendor_id->vendor[0]->id;

        $count = 0;
        foreach ($booking->task as $task) {
            if (!$task->status) {
                $count++;
            }
        }

        if ($count > 0) {
            return response()->json([
                'message' => $count . ' tasks not marked as done'
            ], 422);
        } else {
            if ($booking->paid) {
                if (count($booking->vendor) > 0) {
                    foreach ($booking->vendor as $vendor) {
                        if($vendor->wallet === null){
                            $wallet = new Wallet;
                            $wallet->balance = 0.75 * $booking->base_price;
                            $wallet->user_id = $vendor_id;
                            $wallet->save();
                        }else{
                            $vendor->wallet->increment('balance', 0.75 * $booking->base_price);
                        }

                        $user = User::find($vendor['id']);
                        $user->booking_status = false;
                        $user->save();
                        Mail::to([$user->email])->send(new VendorWalletFunded($user));
                        $pendingNotification = [
                            'title' => 'Cleaning Completed',
                            'body' => 'Your Wallet has been funded after completion of your tasks.'
                        ];
                        
                        $pendingData = [
                            'title' => 'Cleaning Completed',
                            'message' => 'Your Wallet has been funded after completion of your tasks.',
                            'action' => 'in_app',
                            'action_destination' => 'No destination apart from the app'
                        ];
                
                        sendNotification($user->id,$pendingNotification,$pendingData);
                    }

                    Task::where('booking_id', $bookingId)->delete();

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
                }else{
                    return response()->json([
                        'error' => 'No vendor assigned to this booking'
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Please pay for booking schedule before confirming schedule tasks'
                ], 422);
            }

            // Here we close the completed booking and then create a new one for the next schedule

            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new BookingCompleted(Auth()->user()));
           
            $pendingNotification = [
                'title' => 'Cleaning Completed',
                'body' => 'Your Cleaning is completed. Please do leave review for our WhavPros.'
            ];
            
            $pendingData = [
                'title' => 'Cleaning Completed',
                'message' => 'Your Cleaning is completed. Please do leave review for our WhavPros.',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification(Auth()->user()->id,$pendingNotification,$pendingData);

            $booking->status = "completed";
            $booking->completed_by = $vendor_id;
            $booking->save();
            
            if($booking->schedule == 'weekly') {
                $date = strtotime("+7 day");
                $next_cleaning_date = date('Y-m-d', $date);
                $this->createNewBooking($booking,$next_cleaning_date);
            } elseif($booking->schedule == 'bi-weekly') {
                $date = strtotime("+14 day");
                $next_cleaning_date = date('Y-m-d', $date);
                $this->createNewBooking($booking,$next_cleaning_date);
            } elseif($booking->schedule == 'monthly') {
                $date = strtotime("+1 month");
                $next_cleaning_date = date('Y-m-d', $date);
                $this->createNewBooking($booking,$next_cleaning_date);
            } elseif($booking->schedule == 'bi-monthly') {
                $date = strtotime("+2 month");
                $next_cleaning_date = date('Y-m-d', $date);
                $this->createNewBooking($booking,$next_cleaning_date);
            }

            return response()->json([
                'message' => 'All done, vendor has received payment in their wallet'
            ], 200);
        }
    }

    public function getTaskByBookingId($bookingId)
    {
        $booking = Booking::with(['task'])->findOrFail($bookingId);
        $tasks = $booking->task;

        return response()->json([
            'message' => 'Tasks found',
            'data' => $tasks
        ], 200);
    }

    function createNewBooking($completedBooking, $next_cleaning_date)
    {

        $booking = new Booking();
        $booking->save();

        //Get Product & Save product Category
        $product = DB::table('product_booking')->where('booking_id',$completedBooking->id)->first();
        $productCategory = ProductCategory::findOrFail($product->product_id);
        $price = $productCategory->price()->first();

        $booking->products()->attach($productCategory);
        $booking->price()->attach($price);

        // Get Booking Extras
        $bookingExtras = DB::table('booking_extra')->where('booking_id',$completedBooking->id)->get();

        if($bookingExtras != null){
            foreach ($bookingExtras as $bookingExtra) {
                $extra = Extra::findOrFail($bookingExtra->extra_id);
                $booking->extra()->attach($extra);
            }
        }

        // Get Booking Extras Attributes
        $bookingExtrasAttributes = DB::table('booking_extra_attribute')->where('booking_id',$completedBooking->id)->get();
        if($bookingExtrasAttributes != null) {
            foreach ($bookingExtrasAttributes as $bookingExtrasAttribute) {
                $extraAttributes = ExtraAttribute::findOrFail($bookingExtrasAttribute->extra_attribute_id);
                $booking->extraAttribute()->attach($extraAttributes);
            }
        }

         // Get Services
        $bookingServices = DB::table('booking_service')->where('booking_id',$completedBooking->id)->get();
        if($bookingServices != null) {
            foreach ($bookingServices as $bookingService) {
                $service = Service::findOrFail($bookingService->service_id);
                $booking->service()->attach($service);
            }
        }

        // update no_of_rooms, start_date, location, schedule, and time
        $booking->no_of_rooms = $completedBooking->no_of_rooms;
        $booking->schedule = $completedBooking->schedule;
        $booking->location = $completedBooking->location;
        $booking->start_date = $next_cleaning_date;
        $booking->time = $completedBooking->time;
        $booking->address = $completedBooking->address;
        $booking->base_price = $completedBooking->base_price;
        $booking->net_price = $completedBooking->net_price;

        $booking->save();
        Auth()->user()->booking()->save($booking);

        if(Auth()->user()->type == "business") {
            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new NextBookingScheduled(Auth()->user()));

            $pendingNotification = [
                'title' => 'Next Cleaning Scheduled',
                'body' => 'We scheduled your next cleaning. Please ensure you make payment before the day so we can assign Whavpros to you. Thanks For choosing us once again!'
            ];
            
            $pendingData = [
                'title' => 'Next Cleaning Scheduled',
                'message' => 'We scheduled your next cleaning. Please ensure you make payment before the day so we can assign Whavpros to you. Thanks For choosing us once again!',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification(Auth()->user()->id,$pendingNotification,$pendingData);
        } else {
            Mail::to([Auth()->user()->email, 'hello@whavit.com'])->send(new NextBookingScheduled(Auth()->user()));

            $pendingNotification = [
                'title' => 'Next Cleaning Scheduled',
                'body' => 'We scheduled your next cleaning. Please ensure you make payment before the day so we can assign Whavpros to you. Thanks For choosing us once again!'
            ];
            
            $pendingData = [
                'title' => 'Next Cleaning Scheduled',
                'message' => 'We scheduled your next cleaning. Please ensure you make payment before the day so we can assign Whavpros to you. Thanks For choosing us once again!',
                'action' => 'in_app',
                'action_destination' => 'No destination apart from the app'
            ];
    
            sendNotification(Auth()->user()->id,$pendingNotification,$pendingData);
        }

        return true;
        
    }
}
