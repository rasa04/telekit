<?php
namespace Triggers;

use Core\Responses\Trigger;
use Database\models\Group;
use Database\models\User;

class OpenAi extends Trigger {

    private array $messages = [];

    public function __construct($request)
    {
        $group = Group::where('group_id', $request['message']['chat']['id']);
        $user = User::where('user_id', $request['message']['chat']['id']);

        if ($group->first('context')) $this->messages = json_decode($group->first('context')->toArray()["context"], true);
        elseif ($user->first('context')) $this->messages = json_decode($user->first('context')->toArray()["context"], true);

        $this->messages[] = [
            "role" => "user",
            "content" => $request['message']['text']
        ];

        $answer = $this->chat_gpt($this->messages);

        $this->messages[] = [
            "role" => "assistant",
            "content" => $answer
        ];

//        var_dump($this->messages); die();
        $this->messages = array_slice($this->messages, -10, 10);

        if ($group->first('context')) $group->first()->update(['context' => json_encode($this->messages)]);
        elseif ($user->first('context')) $user->first()->update(['context' => json_encode($this->messages)]);

        $this->message()->chat_id($request['message']['chat']['id'])->text($answer)->send();
    }
}