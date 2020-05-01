<?php

use App\User;
use App\Notification;

function sendNotification($userId,$notification,$data)
{
    $url = "https://fcm.googleapis.com/fcm/send";

    $user = User::where('id',$userId)->first();
    $token = $user->device_token;

    $serverKey ='AAAAi33fugc:APA91bGBoIsuwFPKCeBxXUEhKRUll0RqDnCnS16VIkmBJG8qSBmKRQ4aNzziT8A6tN146UJ-9FWNuxVkQ3mCCDLrjlJ7A3sKmEeEkF0CTVnWl00uHexHCnZAI-hi0oZug0I-rGaotFVS';

    $pendingNotification = [
        'title' => $notification['title'],
        'body' => $notification['body']
    ];
    
    $pendingData = [
        'title' => $notification['title'],
        'message' => $data['message'],
        'brand_image_url' => 'https://res.cloudinary.com/whavit/image/upload/v1567552159/whavit-icon_wiobsh.png',
        'action' => $url,
        'action_destination' => $data['action_destination'] //url most times
    ];

    saveNotificaton($user->id,$notification,$data);

    $arrayToSend = [
        'to' => $token,
        'notification' => $pendingNotification,
        'data' => $pendingData,
        'priority'=>'high'
    ];

    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    $result = curl_exec($ch);

    if ($result === FALSE) 
    {
        die('FCM Send Error: ' . curl_error($ch));
    }

    curl_close( $ch );

    return response()->json([
        'message' => 'Notification Sent',
        'data' => $result
    ],200);

}


function saveNotificaton($userId,$newNotification,$data)
{
    $notification = new Notification();
    $notification->user_id = $userId;
    $notification->content = json_encode([$newNotification, $data]);

    if($notification->save()) {
        return true;
    } else {
        return false;
    }
}