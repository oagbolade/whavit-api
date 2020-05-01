<?php

namespace App\Http\Controllers\API\Main;

use App\Discount;
use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    public function checkDiscount($code)
    {
        $discount = Discount::where('code','=',$code)->first();

        //Run check o see if it has not reached maximum usage annd it is still active
        if($discount->no_of_usage == $discount->maximum_usage || $discount->status == false) {
            return response()->json([
                'message' => 'Discount code is not valid',
            ], 500);
        }

        try {

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'discount is valid',
                'percentage' => $discount->percentage,
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 200);
        }
    }

    public function addDiscount($bookingId, $code)
    {
        $booking = Booking::findOrFail($bookingId);
        $discount = Discount::where('code','=',$code)->first();

        //Extract discount value
        $discount_value = 0;
        if($discount->discount_type === "fixed"){
            $discount_value = $discount->fixed_price;
        }elseif($discount->discount_type === "percentage"){
            $discount_value = $discount->percentage;
        }

        //Run check o see if it has not reached maximum usage annd it is still active
        if($discount->no_of_usage == $discount->maximum_usage || $discount->status == false) {
            return response()->json([
                'message' => 'Discount code is not valid',
            ], 500);
        }

        try {
            $booking->discount = $discount_value;
            $booking->save();
            $discount->no_of_usage = $discount->no_of_usage+1;
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'discount added',
                'discount_amount' => $discount_value,
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeDiscount($bookingId, $id)
    {
        $booking = Booking::findOrFail($bookingId);
        $discount = Discount::where('code','=',$code)->first();

        try {
            $booking->discount()->detach($discount);
            $discount->no_of_usage = $discount->no_of_usage-1;
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'discount removed',
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
