<?php
namespace Plots;

use Core\Methods\SendMessage;

class SetEvent {
    public function __construct($request)
    {
        $response = new SendMessage([
            'chat_id' => $request['callback_query']['message']['chat']['id'],
            'text' => 'Напишите имя друга/подруги', 
            'parse_mode' => 'html', 
            'reply_to_message_id' => null,
        ]);
        $response->send();
    }
}