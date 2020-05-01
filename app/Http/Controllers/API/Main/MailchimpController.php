<?php

namespace App\Http\Controllers\API\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailchimpController extends Controller
{
    public function create(Request $request)
    {
        $email = $request->input('email');
        $list_id = 'f7d5212ffe';
        $api_key = env('MAILCHMP_API_KEY','3ec7a98ff381eaec779ad7d95e673987-us20');
        
        $data_center = substr($api_key,strpos($api_key,'-')+1);
        
        $url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $list_id .'/members';
        
        $options = array(
            'FNAME' => $email, 
            'LNAME' => 'whavit'
        );

        $json = json_encode(
            [
            'email_address' => rand(100000,9999999).'@whavit.com',
            'status'        => 'subscribed', //pass 'subscribed' or 'pending'
            'tags' =>  $request->get('tags'),
            'name' => $email,
            'merge_fields' =>  $options
            ]);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($status_code == 200) {
            return response()->json([
                'status' => 'success',
                'message' => 'You have been added to our list successfully '
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'There was an error. Your email must have been used'
            ]);
        }
    }
}
