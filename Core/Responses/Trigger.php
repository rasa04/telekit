<?php

namespace Core\Responses;

use Core\Controllers;
use Core\Env;
use Core\Methods\SendDocument;
use Core\Methods\SendMediaGroup;
use Core\Methods\SendMessage;
use Core\Methods\SendPhoto;

class Trigger
{
    use Controllers;
    use Env;

    public function request(): array
    {
        return $GLOBALS['request'];
    }
    public function sendMessage(): SendMessage
    {
        return new SendMessage;
    }
    public function photo(): SendPhoto
    {
        return new SendPhoto;
    }
    public function document(): SendDocument
    {
        return new SendDocument;
    }
    public function mediaGroup(): SendMediaGroup
    {
        return new SendMediaGroup;
    }
    public function reply_message(string $message): void
    {
        (new SendMessage)
            ->chat_id($GLOBALS['request']['message']['chat']['id'])
            ->text($message)
            ->send();
    }
    public function request_message(): array
    {
        return $GLOBALS['request']['message'];
    }
}