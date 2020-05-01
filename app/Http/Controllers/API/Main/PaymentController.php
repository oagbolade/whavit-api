<?php

namespace App\Http\Controllers\API\Main;

use App\User;
use App\Booking;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function paywithWallet($bookingId)
    {
        $booking = Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId);

        $price = $booking->net_price;
        $discount = $booking->discount;

        $amount_to_deduct = $price - ($price * $discount);
        $wallet = Auth()->user()->wallet;
        
        if ($wallet->balance < $amount_to_deduct) {
            $transaction = new Transaction;
            if (count($booking->products) > 0) :
                $transaction->transaction_id = $booking->products[0]->name;
                $transaction->details = $booking->products[0]->name;
            else :
                //$transaction->transaction = 'Payment for booking';
                $transaction->transaction_id = 'booking_'. str_random(8);
                $transaction->details = 'Payment for booking';
            endif;
            $transaction->amount = $amount_to_deduct;
            $transaction->status = 'fail';

            Auth()->user()->transaction()->save($transaction);

            return response()->json([
                'message' => 'Insufficient funds , add money to wallet and try again',
                'data' => $wallet
            ], 400);
        } else {
            $transaction = new Transaction;
            if (count($booking->products) > 0) :
                $transaction->transaction_id = $booking->products[0]->name;
                $transaction->details = $booking->products[0]->name;
            else :
                $transaction->transaction_id = 'booking_'. str_random(8);
                $transaction->details = 'Payment for booking';
            endif;
            $transaction->amount = $amount_to_deduct;
            $transaction->status = 'success';

            Auth()->user()->transaction()->save($transaction);

            $booking->paid = 1;
            $booking->save();

            Auth()->user()->wallet()->decrement('balance', $amount_to_deduct);

            $wallet = Auth()->user()->wallet;
            return response()->json([
                'message' => 'charge completed',
                'data' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId)
            ], 200);
        }
    }
}
