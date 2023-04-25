<?php
namespace Triggers;

use Core\Database\Database;

class OpenAi extends Trigger {

    private array $messages = [];

    public function __construct($request)
    {
        if ($this->authorized($request, 'chat')) {
            $context = Database::table(table: 'groups', alias: 'g')
                ->column('context')
                ->where('g.group_id = :group_id', 'group_id', $request['message']['chat']['id'])
                ->get()[0]['context'];

            $this->messages = json_decode($context, true);
        }
        elseif ($this->authorized($request, 'user')) {
            $context = Database::table(table: 'users', alias: 'u')
                ->column('context')
                ->where('u.user_id = :user_id', 'user_id', $request['message']['from']['id'])
                ->get()[0]['context'];

            $this->messages = json_decode($context, true);
        }

        $this->messages[] = [
            "role" => "user",
            "content" => $request['message']['text']
        ];

        $answer = $this->chat_gpt($this->messages);

        $this->messages[] = [
            "role" => "assistant",
            "content" => $answer
        ];

        $this->messages = array_slice($this->messages, -10, 10);

        if ($this->authorized($request, 'chat')) {
            Database::table('groups', 'context')
                ->where('group_id = :group_id', 'group_id', $request['message']['chat']['id'])
                ->update(json_encode($this->messages));
        }
        elseif ($this->authorized($request, 'user')) {
            Database::table('users', 'context')
                ->where('user_id = :user_id', 'user_id', $request['message']['from']['id'])
                ->update(json_encode($this->messages));
        }

        $this->message()->chat_id($request['message']['chat']['id'])->text($answer)->send();
    }
}