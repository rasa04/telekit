<?php
namespace Triggers;

use Core\Methods\SendMessage;

class Settings {
    public function __construct($request)
    {
        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text('Настройки')
            ->send();
    }
}