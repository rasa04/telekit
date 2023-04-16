<?php
namespace Triggers;

use Core\Env;
use Core\Methods\SendMessage;
use Core\Controllers;

class OpenAi {
    use Controllers;
    use Env;
    
    private array $name_triggers = [
        'openai ', 'Openai ', 'gpt ', 'Gpt ', 'Рик ', 'рик ',
        'openai , ', 'Openai , ', 'gpt , ', 'Gpt , ', 'Рик , ', 'рик , ',
        'openai, ', 'Openai, ', 'gpt, ', 'Gpt, ', 'Рик, ', 'рик, '
    ];

    private array $users;
    private array $groups = [
        "contexts_of_gigachadchat" => -1001765736589, // гигачад чат
        "contexts_of_what" => -805540894, // че
        "contexts_of_test" =>  -1001673287453 // тестовый чат
    ];

    private array $messages = [];

    public function __construct($request)
    {
        $this->users = $this->pro_users();

        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            // get all contexts from appropriate stored file
            if (isset($request['message']['chat']['id']) && in_array($request['message']['chat']['id'], $this->groups)) {
                foreach ($this->groups as $key => $value) {
                    if ($value == $request['message']['chat']['id']) $this->messages = json_decode(file_get_contents($this->storage() . "ai_contexts/$key.json")) ?? [];
                }
            }
            elseif (isset($request['message']['from']['id']) && in_array($request['message']['from']['id'], $this->users)) {
                $this->messages = json_decode(file_get_contents($this->storage() . "ai_contexts/contexts.json")) ?? [];
            }

            $this->messages[] = [
                "role" => "user",
                "content" => str_replace($this->name_triggers, '', $request['message']['text'])
            ];
            
            // amount of stored contexts
            $this->messages = array_slice($this->messages, -5, 5);
        }
        else {
            $this->messages[] = [
                "role" => "user",
                "content" => str_replace($this->name_triggers, '', $request['message']['text'])
            ];
        }

        //system behaviour
        if (preg_match('(@system|@-sys|@система|@сис)',  $request['message']['text'])) {
            array_pop($this->messages);
            $this->messages[] = [
                "role" => "system",
                "content" => str_replace(['@system ', '@система ', '@сис ', '@sys '], '', $request['message']['text'])
            ];
        }

        $question = [
            "model" => "gpt-3.5-turbo",
            "messages" => $this->messages,
        ];

        $response = $this->client()->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->gpt(),
            ],
            'json' => $question,
            'verify' => false,
        ]);
        $result = json_decode($response->getBody()->getContents(), true)['choices'][0]['message']['content'];

        $answer = (strlen($result) < 4000) ? $result : substr($result, 0, 4000) . "...";

        if (isset($request['message']['from']['id']) || isset($request['message']['chat']['id'])) {
            $this->messages[] = [
                "role" => "assistant",
                "content" => $answer
            ];
            
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