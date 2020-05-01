<?php

namespace App\Http\Controllers\API\Admin;

use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function create(Request $request){
        $this->validate($request,[
            'state' => 'required',
            'name' => 'required'
        ]);

        $location = new Location;

        $location->name = $request->name;
        $location->state =  $request->state;
        
        try {
            $location->save();
        } catch (\Exeception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Location added",
                'data' => $location
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
}
