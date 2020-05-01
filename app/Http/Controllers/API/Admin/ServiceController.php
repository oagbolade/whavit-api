<?php

namespace App\Http\Controllers\API\Admin;

use App\Service;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function addservice(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required' 
        ]);

        $service = new Service;
        $service->price = $request->price;
        $service->title = $request->title;
        $service->description = $request->description;

        try {
            $service->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "service added",
                'data' => $service
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function allservices()
    {
        return response()->json([
            'message' => 'services selected',
            'data' => Service::with(['attributeName.attribute'])->get()
        ], 200);
    }

    public function getservice($id)
    {
        return response()->json([
            'message' => 'service found',
            'data' => Service::with(['attributeName.attribute'])->findOrFail($id)
        ], 200);
    }

    public function addServiceToProduct($product_id,$id)
    {
        $product = ProductCategory::findOrFail($product_id);
        $service = Service::findOrFail($id);

        try {
            $product->service()->attach($service);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'service added',
                'data' => $service
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeServiceFromProduct($product_id,$id){
        $product = ProductCategory::findOrFail($product_id);
        $service = Service::findOrFail($id);

        try {
            $product->service()->detach($service);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'service removed',
                'data' => $service
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
