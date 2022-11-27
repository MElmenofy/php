<?php

function notifybyfirebase($title, $body, $token, $data = [])
{
    $SERVER_API_KEY = "AAAAMbBxsdg:APA91bHR96Vb4NftedJRc7QyuJRCrCIIGCrlzeMv-Wjq-KynUx_HSwoeZktzVOZrUf6IGXnsp5Ri8tYQJXyur1vPpe6j_V7KRxVTRHf81r1iwm4QsZwf37u53s_Ljz0MEqGTsjDIsOOs";
    
    $data = [

        "registration_ids" => [
            $token
        ],

        "notification" => [
            "title" => $title,
            "body" => $body,
            "sound" => "default"
        ],
    ];

    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $response = curl_exec($ch);

}
