<?php

namespace App\Http\Controllers\API\Main;

use App\Service;
use App\ServiceAttribute;
use App\User;
use App\Extra;
use App\AttributeName;
use App\ExtraAttribute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttributeController extends Controller
{
    public function addAttributeName(Request $request, $service_id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $attrName = new AttributeName();

        $attrName->name = $request->name;

        $service = Service::findOrFail($service_id);

        try {
            $service->attributeName()->save($attrName);
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Attribute name added",
                'data' => $attrName
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function addAttributeToBooking($booking_id, $attribute_id)
    {
        $attribute = ServiceAttribute::findOrFail($attribute_id);
        $booking = Booking::findOrFail($booking_id);

        try {
            $booking->attribute()->attach($attribute);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Attribute added",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($booking_id)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeAttributeFromBooking($booking_id, $attribute_id)
    {
        $attribute = ServiceAttribute::findOrFail($attribute_id);
        $booking = Booking::findOrFail($booking_id);

        try {
            $booking->attribute()->detach($attribute);
        } catch (\Execption $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Attribute removed",
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task', 'attribute.attributeName.extra'])->findOrFail($booking_id)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function getAttrNameByService($id)
    {
        $service =Service::findOrFail($id);

        return response()->json([
            'message' => "Found " . count($service->attributeName),
            'data' => $service->attributeName()->with('attribute')->get()
        ], 200);
    }

    public function deleteAttrName($id)
    {
        AttributeName::where('id', $id)->delete();

        return response()->json([
            'message' => "deleted",
        ], 200);
    }

    public function addAttribute(Request $request, $id)
    {
        $this->validate($request, [
            'price' => 'required',
            'measurement' => 'required'
        ]);

        $attrName = AttributeName::findOrFail($id);

        $attr = new ServiceAttribute();
        $attr->price = $request->price;
        $attr->measurement = $request->measurement;

        try {

            $attrName->attribute()->save($attr);
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Attribute added",
                'data' => $attr
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }



    public function getAttrByService($id)
    {
        $service = ServiceAttribute::findOrFail($id);

        return response()->json([
            'message' => "success",
            'data' => $service->attributeName
        ], 200);
    }

    public function deleteAttr($id)
    {
        ServiceAttribute::where('id', $id)->delete();

        return response()->json([
            'message' => "deleted",
        ], 200);
    }

    public function getAttrByAttrName($name)
    {
        $attr = ServiceAttribute::where('type', $name)->get();
        return response()->json([
            'message' => 'found '.count($attr),
            'data' => $attr
        ], 200);
    }
}
