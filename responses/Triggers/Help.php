<?php
namespace Triggers;

use Core\Methods\SendMessage;

class Help {
    public function __construct($request)
    {
        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text('Помощь')
            ->send();
    }
}