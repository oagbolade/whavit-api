<?php

namespace App\Http\Controllers\API\Main;

use App\Service;
use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function addService($bookingId, $id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
        $service = Service::findOrFail($id);

        $price = $service->price;

        $booking->increment('base_price', $price);

        try {
            $booking->service()->attach($service);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
            return response()->json([
                'message' => "Booking service added",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeService($bookingId, $id)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
        $service = Service::findOrFail($id);

        $price = $service->price;

        $booking->decrement('base_price', $price);

        try {
            $booking->service()->detach($service);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($bookingId);
            return response()->json([
                'message' => "Booking service removed",
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
