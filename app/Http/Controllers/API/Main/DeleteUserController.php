<?php

namespace App\Http\Controllers\API\Main;

use DB;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteUserController extends Controller
{
    public function deleteAUser(Request $request)
    {
        $user_id = $request->userId;
        $user = User::findOrFail($user_id);

        if ($user->delete()) {
            return response()->json([
                'message' => 'User Deleted',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Error ocurred, try again'
            ], 400);
        }
    }
}