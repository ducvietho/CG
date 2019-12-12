<?php

namespace App\Traits;


use Illuminate\Support\Facades\Config;

trait PushNotiController
{
    //push notification
    public function pushNotification($key, $data)
    {
        $fields = array();
        $fields['data'] = [
            'data' => $data
        ];
        if (is_array($key)) {
            $fields['registration_ids'] = $key;
        } else {
            $fields['to'] = $key;
        }
        $headers = array
        (
            'Authorization: key=' . Config::get('constants.SERVER_KEY'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
    }


}
