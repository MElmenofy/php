<?php

namespace App\Traits;
use Twilio\Rest\Client;

trait Traits
{

  
public function sendTwilioMessage($phone,$body){ // for sending sms to user, TWILIO
        $twilio_number = env('TWILIO_NUMBER');
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $client = new Client($sid, $token);
        $client->messages->create('+2'.$phone, [
            'from' => $twilio_number,
            'body' => $body]);
    }



}
