<?php
namespace Triggers;

use Core\Methods\SendMessage;
use Core\Consts;
use Core\Controllers;

class OpenAi {
    use Controllers;
    
    private $name_triggers = [
        'openai ', 'Openai ', 'gpt ', 'Gpt ', 'Рик ', 'рик ',
        'openai , ', 'Openai , ', 'gpt , ', 'Gpt , ', 'Рик , ', 'рик , ',
        'openai, ', 'Openai, ', 'gpt, ', 'Gpt, ', 'Рик, ', 'рик, '
    ];
    
    // the users and groups who have access to context
    private $users = [
        511703056,
        748856943,
        250114420,
        272004963
    ];

    private $groups = [
        "contexts_of_gigachadchat" => -1001765736589, // гигачад чат
        "contexts_of_what" => -805540894, // че
        "contexts_of_test" =>  -1001673287453 // тестовый чат
    ];

    private $messages = [];

    public function __construct($request)
    {
        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            // get all contexts from appropriate stored file
            if (isset($request['message']['chat']['id']) && in_array($request['message']['chat']['id'], $this->groups)) {
                foreach ($this->groups as $key => $value) {
                    if ($value == $request['message']['chat']['id']) $this->messages = json_decode(file_get_contents(Consts::STORAGE . "ai_contexts/$key.json")) ?? [];
                }
            }
            elseif (isset($request['message']['from']['id']) && in_array($request['message']['from']['id'], $this->users)) {
                $this->messages = json_decode(file_get_contents(Consts::STORAGE . "ai_contexts/contexts.json")) ?? [];
            }

            array_push($this->messages, [
                "role" => "user",
                "content" => str_replace($this->name_triggers, '' , $request['message']['text'])
            ]);
            
            // amount of stored contexts
            $this->messages = array_slice($this->messages, -5, 5);
        }
        else {
            array_push($this->messages, [
                "role" => "user",
                "content" => str_replace($this->name_triggers, '' , $request['message']['text'])
            ]);
        }

        //system behaviour
        if (preg_match('(@system|@-sys|@система|@сис)',  $request['message']['text'])) {
            array_pop($this->messages);
            array_push($this->messages,[
                "role" => "system",
                "content" => str_replace(['@system ', '@система ', '@сис ', '@sys '], '' , $request['message']['text'])
            ]);
        }

        $question = [
            "model" => "gpt-3.5-turbo",
            "messages" => $this->messages,
        ];

        $response = $this->client()->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . Consts::GPT,
            ],
            'json' => $question,
            'verify' => false,
        ]);
        $result = json_decode($response->getBody()->getContents(), true);

        $answer = $result['choices'][0]['message']['content'];

        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            array_push($this->messages, [
                "role" => "assistant",
                "content" => $answer
            ]);
            
            // amount of stored contexts
            $this->messages = array_slice($this->messages, -7, 7);

            // save new context to stored file
            if (isset($request['message']['chat']['id']) && in_array($request['message']['chat']['id'], $this->groups)) {
                foreach ($this->groups as $key => $value) {
                    if ($value == $request['message']['chat']['id']) $this->saveDataToJson($this->messages, "ai_contexts/$key.json", true);
                }
            }
            elseif (isset($request['message']['from']['id']) && in_array($request['message']['from']['id'], $this->users)) {
                $this->saveDataToJson($this->messages, 'ai_contexts/contexts.json', true);
            }
        }

        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text($answer)
            ->send();
    }
}