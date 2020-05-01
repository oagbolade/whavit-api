<?php

namespace App\Http\Controllers\API\Main;

use App\Extra;
use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExtraController extends Controller
{
    public function addExtra($bookingId, $id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
        $extra = Extra::findOrFail($id);

        $price = $extra->price;

        $booking->increment('base_price', $price);

        try {
            $booking->extra()->attach($extra);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
            return response()->json([
                'message' => "Booking extra added",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeExtra($bookingId, $id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
        $extra = Extra::findOrFail($id);

        $price = $extra->price;

        $booking->decrement('base_price', $price);

        try {
            $booking->extra()->detach($extra);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
            return response()->json([
                'message' => "Booking extra removed",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
