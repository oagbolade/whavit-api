<?php

namespace App\Http\Controllers\API\Admin;

use App\Price;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function allCategories()
    {
        return response()->json([
            'message' => 'categories selected',
            'data' => ProductCategory::where('disabled', 0)->with(['extra','area','service','price'])->get()
        ], 200);
    }

    public function getCategory($id)
    {
        return response()->json([
            'message' => 'category found',
            'data' => ProductCategory::with(['extra','area','service','price'])->findOrFail($id)
        ], 200);
    }

    public function createCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'price' => 'integer'
        ]);

        $category = new ProductCategory;

        $category->name = $request->name;
        $category->alias = 'alias';
        $category->description = $request->description;

        $price = new Price;
        
        if(!is_null($request->price)){
            $price->default = $request->price;
        }
        try {
            $category->save();
            $category->price()->save($price);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Category added",
                'data' => ProductCategory::with(['extra','area','service','price'])->findOrFail($category->id)
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function disableCategory($id)
    {
        $category = ProductCategory::findOrFail($id);

        $category->disabled = true;

        try {
            $category->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Category Disabled", 
                'data' => $category
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function updatePrice(Request $request,$product_id)
    {
        $this->validate($request,[
            'one' => 'required|integer',
            'two' => 'required|integer',
            'three' => 'required|integer',
            'four' => 'required|integer',
            'five' => 'required|integer',
            'six' => 'required|integer',
            'seven' => 'required|integer',
            'eight' => 'required|integer',
            'nine' => 'required|integer',
            'ten' => 'required|integer',
        ]);
        $price = ProductCategory::findOrFail($product_id)->price()->first();

        $price->one = $request->one;
        $price->two = $request->two;
        $price->three = $request->three;
        $price->four = $request->four;
        $price->five = $request->five;
        $price->six = $request->six;
        $price->seven = $request->seven;
        $price->eight = $request->eight;
        $price->nine = $request->nine;
        $price->ten = $request->ten;

        try {
            $price->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Price Updated",
                'data' => $price
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
        
    }
    
}
