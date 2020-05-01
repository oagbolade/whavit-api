<?php

namespace App\Http\Controllers\API\Admin;

use App\Discount;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    public function createDiscount(Request $request){
        $this->validate($request,[
            'code' => 'required',
            'description' => 'required',
            'discount_value' => 'required',
            'discount_type' => 'required',
            'maximum_usage' => 'required'
        ]);
        
        $discount = new Discount();
        $discount->code =  $request->code;
        $discount->description =  $request->description;

        if($request->discount_type === "percentage"){
            $discount->percentage = $request->discount_value/100;
        }else{
            $discount->fixed_price = $request->discount_value;
        }
        
        $discount->discount_type = $request->discount_type;
        $discount->maximum_usage = $request->maximum_usage;

        try {
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Discount added",
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function getDiscounts(){
        return response()->json([
            'message' => 'all discounts selected',
            'data' => Discount::all()
        ], 200);
    }

    public function getActiveDiscounts(){
        return response()->json([
            'message' => 'active discounts selected',
            'data' => Discount::where('status',true)->get()
        ], 200);
    }

    public function activateDiscount($id){
        $discount = Discount::findOrFail($id);

        $discount->status = true;

        try {
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Discount activated",
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    
    public function deactivateDiscount($id){
        $discount = Discount::findOrFail($id);

        $discount->status = false;

        try {
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Discount deactivated",
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function deleteDiscount($id){
        $discount = Discount::findOrFail($id);

        try {
            $discount->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Discount deleted",
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function editDiscount(Request $request,$id){
        $this->validate($request,[
            'code' => 'required',
            'description' => 'required',
            'percentage' => 'required|integer|min:0|max:100',
            'maximum_usage' => 'required'
        ]);

        $discount = Discount::findOrFail($id);
        $discount->code =  $request->code;
        $discount->description =  $request->description;
        $discount->percentage = $request->percentage/100;
        $discount->maximum_usage = $request->maximum_usage;

        try {
            $discount->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Discount Updated",
                'data' => $discount
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    
    // public function addDiscountToProduct($product_id,$id){
    //     $product = ProductCategory::findOrFail($product_id);
    //     $discount = Discount::findOrFail($id);

    //     try {
    //         $product->discount()->attach($discount);
    //     } catch (\Exception $e) {
    //         $error = $e->getMessage();
    //         $errorCode = $e->getCode();
    //     }

    //     if (!isset($error)) {
    //         return response()->json([
    //             'message' => 'discount added',
    //             'data' => ProductCategory::findOrFail($product_id)->with('discount')->get()
    //         ],200);
    //     } else {
    //         return response()->json([
    //             'message' => $error,
    //         ], 500);
    //     }
    // }

    // public function removeDiscountFromProduct($product_id,$id){
    //     $product = ProductCategory::findOrFail($product_id);
    //     $discount = Discount::findOrFail($id);

    //     try {
    //         $product->discount()->detach($discount);
    //     } catch (\Exception $e) {
    //         $error = $e->getMessage();
    //         $errorCode = $e->getCode();
    //     }

    //     if (!isset($error)) {
    //         return response()->json([
    //             'message' => 'discount removed',
    //             'data' => ProductCategory::findOrFail($product_id)->with('discount')->get()
    //         ],200);
    //     } else {
    //         return response()->json([
    //             'message' => $error,
    //         ], 500);
    //     }
    // }
}
