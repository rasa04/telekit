<?php
namespace Triggers;

use Core\Methods\SendMessage;
use Core\Consts;
use Core\Controllers;

class OpenAi {
    use Controllers;
    public function __construct($request)
    {
        // the users and groups who have access to context
        $users = [511703056, 748856943];
        $groups = [
            "contexts_of_gigachadchat" => -1001765736589, // гигачад чат
            "contexts_of_what" => -805540894, // че
            "contexts_of_test" =>  -1001673287453 // тестовый чат
        ];

        $messages = [];

        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            // get all contexts from appropriate stored file
            if (isset($request['message']['chat']['id']) && in_array($request['message']['chat']['id'], $groups)) {
                foreach ($groups as $key => $value) {
                    if ($value == $request['message']['chat']['id']) $messages = json_decode(file_get_contents(Consts::STORAGE . "ai_contexts/$key.json")) ?? [];
                }
            }
            elseif (isset($request['message']['from']['id']) && in_array($request['message']['from']['id'], $users)) {
                $messages = json_decode(file_get_contents(Consts::STORAGE . "ai_contexts/contexts.json")) ?? [];
            }

            array_push($messages, [
                "role" => "user",
                "content" => $request['message']['text']
            ]);

            // amount of stored contexts
            $messages = array_slice($messages, -5, 5);
        }
        else {
            array_push($messages, [
                "role" => "user",
                "content" => str_replace(['openai', 'Openai', 'gpt ', 'Gpt ', 'Рик', 'рик'], '' , $request['message']['text'])
            ]);
        }

        $question = [
            "model" => "gpt-3.5-turbo",
            "messages" => $messages,
        ];

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
            CURLOPT_POSTFIELDS => json_encode($question)
        ]);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);

        $answer = $result['choices'][0]['message']['content'];

        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            array_push($messages, [
                "role" => "assistant",
                "content" => $answer
            ]);
            
            // amount of stored contexts
            $messages = array_slice($messages, -5, 5);

            // save new context to stored file
            if (isset($request['message']['chat']['id']) && in_array($request['message']['chat']['id'], $groups)) {
                foreach ($groups as $key => $value) {
                    if ($value == $request['message']['chat']['id']) $this->saveDataToJson($messages, "ai_contexts/$key.json", true);
                }
            }
            elseif (isset($request['message']['from']['id']) && in_array($request['message']['from']['id'], $users)) {
                $this->saveDataToJson($messages, 'ai_contexts/contexts.json', true);
            }
        }

        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text($answer)
            ->send();
    }
}