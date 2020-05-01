<?php

namespace App\Http\Controllers\API\Admin;

use App\Clean;
use App\Area;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    public function allAreas()
    {
        return response()->json([
            "message" => "areas selected",
            "data" => Area::with('clean')->get()
        ], 200);
    }

    public function addArea(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $area = new Area();
        $area->title = $request->title;
        $area->description = 'description';

        try {
            $area->save();

        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Area added",
                'data' => $area
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function getArea($id)
    {
        $area = Area::with('clean')->findOrFail($id);
        return response()->json([
            "message" => "area found",
            "data" => $area
        ], 200);
    }

    public function addCleanToArea(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);
        $area = Area::findOrFail($id);

        $clean = new Clean;

        $clean->title = $request->title;

        try {
            $area->clean()->save($clean);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Clean added",
                'data' => $area
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function addAreaToProduct($product_id,$id){
        $product = ProductCategory::findOrFail($product_id);
        $area = Area::findOrFail($id);

        try {
            $product->area()->attach($area);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'area added',
                'data' => $area
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeAreaFromProduct($product_id,$id){
        $product = ProductCategory::findOrFail($product_id);
        $area = Area::findOrFail($id);

        try {
            $product->area()->detach($area);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'area removed',
                'data' => $area
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

}
