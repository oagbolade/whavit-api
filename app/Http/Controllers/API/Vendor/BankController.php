<?php

namespace App\Http\Controllers\API\Vendor;

use App\Bank;
use App\User;
use App\Location;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function addBank(Request $request)
    {

        $this->validate($request, [
            'account_name' => 'required',
            'account_number' => 'required',
            'bank_code' => 'required',
            'bank_name' => 'required'
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
            $http_request = $paystack->request('POST', 'https://api.paystack.co/transferrecipient', [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . paystack_secret_key()
                ]
            ]);
            $response = $http_request->getBody();
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
                'message' => $error
            ], $errorCode);
        } else {

            $bank = new Bank();
            $bank->account_name = $request->account_name;
            $bank->account_number = $request->account_number;
            $bank->bank_code = $request->bank_code;
            $bank->bank_name = $request->bank_name;
            $bank->recipient_code = $response->data->recipient_code;

            $user = Auth()->user();

            try {
                $user->bank()->save($bank);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            if (!isset($error)) {
                return response()->json([
                    'data' => $bank,
                    'message' => 'bank added'
                ], 200);
            } else {
                return response()->json([
                    'message' => $error
                ], 500);
            }
        }
    }

    public function changeBankName(Request $request, $id)
    {
        $this->validate($request, [
            'account_name' => 'required'
        ]);

        $bank = Bank::findOrFail($id);

        $bank->account_name = $request->account_name;

        try {
            $bank->save();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if (!isset($error)) {
            return response()->json([
                'data' => $bank,
                'message' => 'Account name updated'
            ], 200);
        } else {
            return response()->json([
                'message' => $error
            ], 500);
        }
    }

    public function getVendorBanks()
    {
        $user = Auth()->user();

        $banks = $user->bank;

        return response()->json([
            'data' => $banks
        ], 200);
    }
}
