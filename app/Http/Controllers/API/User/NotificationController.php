<?php

namespace App\Http\Controllers\API\User;

use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function list($id)
    {
        $notifications = Notification::where('user_id',$id)->get();

        if(count($notifications) >= 1) {
            return response()->json([
                'message' => "User has notifications",
                'data' => $notifications
            ], 200);
        } else {
            return response()->json([
                'message' => 'User has no notification',
            ], 200);
        }
    }
}
