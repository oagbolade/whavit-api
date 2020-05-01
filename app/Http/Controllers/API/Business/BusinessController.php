<?php

namespace App\Http\Controllers\API\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class BusinessController extends Controller
{
    //

    public function showAll(){
        return response()->json([
            "message" => "Business Representatives fetched",
            "data" => User::where('type','business')->get()
        ],200);
    }

    public function showOne($id){
        $business = User::findOrFail($id);
        
        if($business->isRep()){
            return response()->json([
                "message" => "Business Representative found",
                "data" => $business
            ],200);
        }else{
            return response()->json([
                'message' => 'Selected user is not a Business Representative'
            ],404);
        }
    }
}
