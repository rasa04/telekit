<?php
namespace Triggers;

use Core\Methods\SendMessage;

class GetApi {
    public function __construct($request)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.ipify.org?format=json",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        $response = new SendMessage;
        if (isset($result)) {  
            $response
                ->chat_id($request['message']['chat']['id'])
                ->text($result)
                ->parse_mode()
                ->send();
        }
        else {
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text("api не обнаружен")
            ->parse_mode()
            ->send();
        }
    }
}