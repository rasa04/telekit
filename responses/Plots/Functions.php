<?php
namespace Plots;

use Core\Methods\SendMessage;

class Functions {
    public function __construct($request)
    {
        $response = new SendMessage([
            'chat_id' => $request['callback_query']['message']['chat']['id'],
            'text' => 'Функции', 
            'parse_mode' => 'html', 
            'reply_to_message_id' => null,
            'reply_markup' => [
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
                'keyboard' => [
                    [
                        ['text' => 'Языки'],
                        ['text' => 'прочее'],
                    ],
                    [
                        ['text' => 'прочее'],
                        ['text' => 'прочее'],
                    ]
                ]
            ],
        ]);
       $response->send();
    }
}