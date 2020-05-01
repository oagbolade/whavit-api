<?php

namespace App\Http\Controllers\API\User;

use App\User;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function getUserTransactions()
    {
        $user = Auth()->user();

        $transactions = $user->transaction;

        return response()->json([
            'message' => 'ok',
            'data' => $transactions
        ], 200);
    }
}
