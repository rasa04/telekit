<?php
namespace Triggers;

use Core\Methods\SendMessage;

class OpenAi {
    public function __construct($request)
    {
        $prompt = str_replace(['openai', 'Openai', 'gpt ', 'Gpt ', 'Рик', 'рик'], '' , $request['message']['text']);

        $raw = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ];

        $postfields = json_encode($raw);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer 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"
            ],
            CURLOPT_POSTFIELDS => $postfields
        ]);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        $answer = $result['choices'][0]['message']['content'];

        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text($answer)
            ->send();
    }
}