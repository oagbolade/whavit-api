<?php

namespace App\Http\Controllers\API\Admin;

use App\Extra;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExtraController extends Controller
{
    public function addExtra(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        $extra = new Extra;
        $extra->price = $request->price;
        $extra->title = $request->title;
        $extra->description = $request->description;

        try {
            $extra->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Extra added",
                'data' => $extra
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function allExtras()
    {
        return response()->json([
            'message' => 'extras selected',
            'data' => Extra::all()
        ], 200);
    }

    public function getExtra($id)
    {
        return response()->json([
            'message' => 'extra found',
            'data' => Extra::with(['attributeName.attribute'])->findOrFail($id)
        ], 200);
    }

    public function addExtraToProduct($product_id,$id){
        $product = ProductCategory::findOrFail($product_id);
        $extra = Extra::findOrFail($id);

        try {
            $product->extra()->attach($extra);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'extra added',
                'data' => $extra
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function removeExtraFromProduct($product_id,$id)
    {
        $product = ProductCategory::findOrFail($product_id);
        $extra = Extra::findOrFail($id);

        try {
            $product->extra()->detach($extra);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => 'extra removed',
                'data' => $extra
            ],200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
