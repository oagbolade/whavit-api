<?php

namespace App\Http\Controllers\API\Vendor;

use Carbon\Carbon;
use App\Bank;
use App\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayoutController extends Controller
{
    public function createRecipient(Request $request)
    {
        $this->validate($request, [
            'bank_code' => 'required',
            'account_number' => 'required|digits:10',
        ]);

        $paystack = new Client([
            'timeout' => 15
        ]);

        $body = [
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_code' => $request->bank_code
        ];

        $body  = json_encode($body);

        try {
            $request = $paystack->request('POST', 'https://api.paystack.co/transferrecipient', [
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
        } catch (RequestException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], $errorCode);
        } else {
            return response()->json($response);
        }
    }

    protected function disableOtp()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transfer/disable_otp",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authorization: Bearer ".paystack_secret_key()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        return $response;
    }

    public function finalTransfer()
    {
        $paystack = new Client([
            'timeout' => 15
        ]);

        $body = [
            "otp" => '367911'
        ];

        $body  = json_encode($body);

        try {
            $request = $paystack->request('POST', 'https://api.paystack.co/transfer/disable_otp_finalize', [
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
        } catch (RequestException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], $errorCode);
        } else {
            return response()->json($response);
        }
    }

    public function disburseFund(Request $request)
    {
        $this->validate($request, [
            'bank_id' => 'required',
            'amount' => 'required|integer'
        ]);

        $today = new Carbon();
        if($today->dayOfWeek != Carbon::MONDAY) {
            return response()->json([
                'message' => 'Today is not payout day. Check back on Monday'
            ], 404);
        }

        $user = Auth()->user();
        $wallet = $user->wallet;

        if($wallet->balance < $request->amount) {
            return response()->json([
                'message' => 'Your wallet balance does not match up with the amount requested'
            ], 200);
        }

        $amount = $request->amount;
        $bank = Bank::findOrFail($request->bank_id);
        $recipient_code = $bank->recipient_code;

        $paystack = new Client([
            'timeout' => 15
        ]);

        $body = [
            "source" => "balance",
            "description" => "Withdrawal disbursement",
            "amount" => $request->amount."00",
            "recipient" => $recipient_code
        ];

        $body  = json_encode($body);

        try {
            $request = $paystack->request('POST', 'https://api.paystack.co/transfer', [
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
        } catch (RequestException $e) {
            $error = $e->getMessage();
            $errorCode = $e->getCode();
        }

        if (isset($error)) {
            return response()->json([
                'message' => stripslashes($error)
            ], $errorCode);
        } else {
            $transaction = new Transaction();
            $transaction->user_id = $bank->user_id;
            $transaction->transaction_id = $response->data->reference;
            $transaction->details = 'withdrawal of funds';
            $transaction->status = 'success';
            $transaction->payment_gateway = 'Paystack';
            $transaction->payment_gateway_fee = 50;
            $transaction->amount = $amount;

            $transaction->save();

            return response()->json($response);
        }
    }

    public function list()
    {
        $transactions = Transaction::where('details','withdrawal of funds')->get();

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

    public function listByUser($userId)
    {
        $transactions = Transaction::where('details','withdrawal of funds')->where('user_id',$userId)->get();

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
