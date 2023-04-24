<?php
namespace Triggers;

use Core\Database\Database;
use Core\Storage\Storage;
use Doctrine\DBAL\Exception;

class OpenAi extends Trigger {
    private array $name_triggers = [
        'openai ', 'Openai ', 'gpt ', 'Gpt ', 'Рик ', 'рик ',
        'openai , ', 'Openai , ', 'gpt , ', 'Gpt , ', 'Рик , ', 'рик , ',
        'openai, ', 'Openai, ', 'gpt, ', 'Gpt, ', 'Рик, ', 'рик, '
    ];

    private array $messages = [];

    /**
     * @throws Exception
     */
    public function __construct($request)
    {
        $database = new Database;
        if ($this->authorized($request, 'chat')) {
            $name = array_search($request['message']['chat']['id'], $this->pro_chats(), true);
            if ($name) $this->messages = Storage::get("ai_contexts/$name.json");
        }
        elseif ($this->authorized($request, 'user')) {
            $this->messages = Storage::get("ai_contexts/contexts.json");
        }

        $this->messages[] = [
            "role" => "user",
            "content" => str_replace($this->name_triggers, '', $request['message']['text'])
        ];

        $answer = $this->chat_gpt($this->messages);

        $this->messages[] = [
            "role" => "assistant",
            "content" => $answer
        ];

        $this->messages = array_slice($this->messages, -10, 10);

        if ($this->authorized($request, 'chat')) {
            $name = array_search($request['message']['chat']['id'], $this->pro_chats(), true);
            if ($name) Storage::save($this->messages, "ai_contexts/$name.json", true);
        }
        elseif ($this->authorized($request, 'user')) {
            Storage::save($this->messages, 'ai_contexts/contexts.json', true);
        }

        $this->response()->chat_id($request['message']['chat']['id'])->text($answer)->send();
    }
}