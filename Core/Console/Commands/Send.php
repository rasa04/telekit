<?php

namespace Core\Console\Commands;
use Core\Methods\SendMessage;

class Send extends CommandAbstract
{
    public function __construct($options, $argv)
    {
        $chat_id = $options["to"] ?? getenv("DEFAULT_USER");
        $message = $options["message"] ?? "Hi! It's test message from: " . getenv("APP_NAME");
        (new SendMessage)
            ->chat_id($chat_id)
            ->text($message)
            ->send();
        $this->response($options);
    }
}