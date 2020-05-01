<?php

namespace App\Http\Controllers\API\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function allAdmins()
    {
        $admins = User::where('type','admin_one')->get();

        return response()->json($admins,200);
    }

}
