<?php

namespace App\Http\Controllers\API\User;

use App\User;
use App\Card;
use App\Booking;
use App\Transaction;
use App\Wallet;
use App\Mail\VendorTip;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Http\Client\Exception\RequestException;
use App\Mail\WalletFunded;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{
    protected $key_public;
    protected $key_secret;


    public function __construct()
    {
        $this->key_public = env("PAYSTACK_PUBLIC");
        $this->key_secret = env("PAYSTACK_SECRET");
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Auth()->user();

        return response()->json([
            'message' => 'ok',
            'data' => $user->wallet
        ], 200);
    }

    public function addToWallet(Request $request)
    {
        #https://api.paystack.co/transaction/charge_authorization
        $this->validate($request, [
            'amount' => 'required|integer',
            'card_id' => 'required'
        ]);

        $user = Auth()->user();
        $email = $user->email;
        $amount = $request->amount . '00';
        $amount = (int) $amount;
        $card = Card::findOrFail($request->card_id);
        $authCode = $card->auth_code;
        //attempt to charge card
        $paystack = new Client([
            'timeout' => 15
        ]);

        $body = [
            'email' => $email,
            'amount' => $amount,
            'reference' => str_random(30),
            'callback_url' => $request->callback_url,
            'authorization_code' => $authCode
        ];
        $body  = json_encode($body);

        try {
            $request = $paystack->request('POST', 'https://api.paystack.co/transaction/charge_authorization', [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . paystack_secret_key()
                ]
            ]);
            $response = $request->getBody();
            $response = json_decode($response);
        } catch (ConnectException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        } catch (ClientException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }catch (RequestException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], 500);
        } else {
            $responseData = $response->data;
            if ($responseData->status == 'success') {
                $wallet = Auth()->user()->wallet;
                $wallet->increment('balance', substr($responseData->amount, 0, -2));

                $transaction = new Transaction();
                $transaction->user_id = Auth()->user()->id;
                $transaction->transaction_id = $response->data->reference;
                $transaction->amount = $response->data->amount/100;
                $transaction->details = "Wallet";
                $transaction->status = "success";

                $transaction->save();

                Mail::to([Auth()->user()->email])->send(new WalletFunded(Auth()->user()));

                return response()->json([
                    'message' => 'success',
                    'paystack_response' =>  $response,
                    'wallet' => $wallet
                ], $request->getStatusCode());
            } else if ($responseData->status == 'failed') {
                return response()->json([
                    'message' => 'failed',
                    'reason' => $responseData->gateway_response,
                    'paystack_response' =>  $response,
                    'wallet' => $wallet
                ], $request->getStatusCode());
            } else {
                return response()->json([
                    'message' => 'failed',
                    'paystack_response' =>  $response,
                    'wallet' => $wallet
                ], $request->getStatusCode());
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user = Auth()->user();
        $wallet = $user->wallet;

        if ($request->type == 'credit') {
            $wallet->increment('balance', $request->amount);
        } else if ($request->type == 'debit') {
            $wallet->decrement('balance', $request->amount);
        }

        try {
            $user->wallet()->save($wallet);
        } catch (\Exeception $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }


        if (!isset($error)) {
            return response()->json([
                'message' => "wallet updated",
                'data' => $wallet
            ], 200);
        } else {
            return response()->json([
                'message' => $error,
            ], 500);
        }
    }

    public function initializeTransaction(Request $request)
    {
        $this->validate($request, [
            'callback_url' => 'required',
            'amount' => 'integer|min:50', //amount in niara,
        ]);

        $bookingId = $request->booking_id;

        if (isset($request->amount)) {
            $amount = $request->amount . '00';
        } else {
            $amount = 5000;
        }
        $email = Auth()->user()->email;
        $paystack = new Client([
            'timeout' => 15
        ]);

        $body = [
            'email' => $email,
            'amount' => $amount,
            'reference' => str_random(30),
            'callback_url' => $request->callback_url
        ];
        $body  = json_encode($body);

        try {
            $request = $paystack->request('POST', 'https://api.paystack.co/transaction/initialize', [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . paystack_secret_key()
                ]
            ]);
            $response = $request->getBody();
            $response = json_decode($response);
        } catch (ConnectException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        } catch (ClientException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], 500);
        }
        
        // update paid here
        if(isset($bookingId)){
            $booking = Booking::with(['extra', 'products.area', 'user', 'service','vendor','task'])->findOrFail($bookingId);
            $booking->paid = 1;
            $booking->save();
            return response()->json($response, $request->getStatusCode());
        }
        
        return response()->json($response, $request->getStatusCode());
    }

    public function verifyTransaction(Request $request)
    {
        $reference = $request->get('reference');

        $paystack = new Client([
            'timeout' => 10
        ]);

        try {
            $request = $paystack->request('GET', 'https://api.paystack.co/transaction/verify/' . $reference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . paystack_secret_key()
                ]
            ]);

            $response = $request->getBody();
            $response = json_decode($response);
        } catch (ClientException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        } catch (ConnectException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], 500);
        } else {
            $transactionStatus = $response->data->status;
            $amount = $response->data->amount;
            $responseData = $response->data;
            $gatewayResponse = $response->data->gateway_response;
            $authorization = $response->data->authorization;

            if ($response->status) {
                if ($responseData->status == 'success') {
                    $wallet = Auth()->user()->wallet;
                    $wallet->increment('balance', substr($amount, 0, -2));
                    // manipulate db wallet
                    $authCode = $authorization->authorization_code;
                    //Save Authorizatio code for later use and 
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);
                    //store authorization Json to show saved card
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment Successful',
                        'data' => $response->data,
                        'card' => $card
                    ], 200);
                } else if ($responseData->status == 'failed') {
                    $authCode = $authorization->authorization_code;
                    //Save Authorizatio code for later use and 
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);
                    //store authorization Json to show saved card
                    return response()->json([
                        'status' => 'failed',
                        'message' => $gatewayResponse,
                        'data' => $response->data,
                        'card' => $card
                    ], 200);
                } else {
                    $authCode = $authorization->authorization_code;
                    //Save Authorizatio code for later use and 
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);
                    //store authorization Json to show saved card
                    return response()->json([
                        'status' => 'failed',
                        'message' => $gatewayResponse,
                        'data' => $response->data,
                        'card' => $card
                    ], 200);
                }
            } else {
                return response()->json($response, $request->getStatusCode());
            }
        }
    }

    public function verifyTransactionForDirectPayment(Request $request)
    {
        $reference = $request->get('reference');
        $bookingId = $request->get('booking_id');
        
        $booking = Booking::with(['extra', 'products.area', 'user', 'service','vendor','task'])->findOrFail($bookingId);

        $paystack = new Client([
            'timeout' => 10
        ]);

        try {
            $request = $paystack->request('GET', 'https://api.paystack.co/transaction/verify/' . $reference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . paystack_secret_key()
                ]
            ]);

            $response = $request->getBody();
            $response = json_decode($response);
        } catch (ClientException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        } catch (ConnectException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], 500);
        } else {
            $transactionStatus = $response->data->status;
            $amount = $response->data->amount/100;
            $responseData = $response->data;
            $gatewayResponse = $response->data->gateway_response;
            $authorization = $response->data->authorization;

            if ($response->status) {
                if ($responseData->status == 'success') {
                    //Save card details
                    
                    $authCode = $authorization->authorization_code;
                    
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);

                    $transaction = new Transaction;
                    if (count($booking->products) > 0) :
                        $transaction->transaction_id = $booking->products[0]->name.str_random(8);
                        $transaction->details = $booking->products[0]->name;
                    else :
                        $transaction->transaction_id = 'booking'.str_random(8);
                        $transaction->details = 'Payment for booking';
                    endif;
                    $transaction->amount = $amount;
                    $transaction->status = 'success';

                    Auth()->user()->transaction()->save($transaction);

                    $booking->paid = 1;
                    $booking->save();

                    return response()->json([
                        'message' => 'charge completed',
                        'booking' => Booking::with(['extra', 'products.area', 'user', 'service', 'vendor', 'task'])->findOrFail($bookingId),
                        'data' => $response->data,
                        'card' => $card
                    ], 200);

                    
                } else if ($responseData->status == 'failed') {
                    $authCode = $authorization->authorization_code;
                    //Save Authorizatio code for later use and 
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);
                    //store authorization Json to show saved card
                    return response()->json([
                        'status' => 'failed',
                        'message' => $gatewayResponse,
                        'data' => $response->data,
                        'card' => $card
                    ], 200);
                } else {
                    $authCode = $authorization->authorization_code;
                    //Save Authorizatio code for later use and 
                    $card = new Card;

                    $user = Auth()->user();
                    $card->auth_code = $authCode;
                    $card->card_name = $authorization->bank . ' ' . $authorization->card_type;
                    $card->bank = $authorization->bank;
                    $card->card_type = $authorization->card_type;
                    $card->last_4 = $authorization->last4;
                    $card->expiry = $authorization->exp_month . ' ' . $authorization->exp_year;
                    $user->card()->save($card);
                    //store authorization Json to show saved card
                    return response()->json([
                        'status' => 'failed',
                        'message' => $gatewayResponse,
                        'data' => $response->data,
                        'card' => $card
                    ], 200);
                }
            } else {
                return response()->json($response, $request->getStatusCode());
            }
        }
    }

    public function sendTipFromWallet(Request $request,$vendor_id){
        $this->validate($request,[
            'amount' => 'required|integer'
        ]);

        $vendor = User::findOrFail($vendor_id);
        $user = Auth()->user();
        
        if($user->wallet->balance < $request->amount){
            return response()->json([
                'message' => 'Insufficient fund'
            ],422);
        }

        try {
            $vendor->wallet->increment('balance',$request->amount);
            $user->wallet->decrement('balance',$request->amount);
        }catch(\Exception $e){
            $error = $e->getMessage();
        }

        if(!isset($error)){
            Mail::to([$vendor->email])->send(new VendorTip($vendor));
            return response()->json([
                'data' => $request->amount,
                'message' => 'Tip sent'
            ],200);
        }else{
            return response()->json([
                'message' => $error
            ],500);
        }
    }
}
