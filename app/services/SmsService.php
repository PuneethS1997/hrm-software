<?php

class SmsService {

  public static function send($mobile, $message) {
    $apiKey = 'YOUR_SMS_API_KEY';

    $url = "https://www.fast2sms.com/dev/bulkV2";
    $data = [
      "route" => "q",
      "message" => $message,
      "numbers" => $mobile,
    ];

    $headers = [
      "authorization: $apiKey",
      "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
  }
}
