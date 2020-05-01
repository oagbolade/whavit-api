<?php

namespace App\Http\Controllers\API\Admin;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        $this->validate($request,[
            'transaction' => 'required',
            'details'=> 'max:255',
            'status' => 'required|in:failed,success'
            
        ]);
        $user = Auth()->user();

        $transaction = new Transaction;

        $transaction->transaction_id = $request->transaction;
        $transaction->details = $request->details;
        $transaction->status = $request->status;
       
        try {
            $user->transaction()->save($transaction);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (!isset($error)) {
            return response()->json([
                'message' => "Transaction added",
                'data' => $transaction
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }
    
    public function show()
    {
        $transactions = Transaction::all();

        if ($transactions) {
            return response()->json([
                'message' => "sucess",
                'data' => $transactions
            ], 200);
        } else {
            return response()->json([
                'message' => 'There is no transaction yet',
            ], 200);
        }
    }
}
