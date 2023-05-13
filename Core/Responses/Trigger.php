<?php

namespace Core\Responses;

use Core\Console\Commands\Send;
use Core\Controllers;
use Core\Env;
use Core\Methods\DeleteMessage;
use Core\Methods\SendDocument;
use Core\Methods\SendInvoice;
use Core\Methods\SendMediaGroup;
use Core\Methods\SendMessage;
use Core\Methods\SendPhoto;
use Database\models\Chat;
use Illuminate\Database\Eloquent\Model;

class Trigger
{
    use Controllers;
    use Env;

    public array $lastMessage;

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
        $this->lastMessage = (new SendMessage)
            ->chat_id($GLOBALS['request']['message']['chat']['id'])
            ->text($message)
            ->parse_mode()
            ->send();
    }
    public function request_message(): array
    {
        return $GLOBALS['request']['message'];
    }

    public function send_invoice(): SendInvoice
    {
        return new SendInvoice;
    }

    public function deleteMessage($message_id = null): void
    {
        if ($message_id === null) {
            $message_id = $this->lastMessage['result']['message_id'];
        }
        (new DeleteMessage())->delete($message_id);
    }
}