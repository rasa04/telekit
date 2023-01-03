<?php
namespace Core\Templates;

use Core\Methods\SendMessage;

class SetBirthday {
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