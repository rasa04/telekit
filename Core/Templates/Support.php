<?php
namespace Core\Templates;

use Core\Methods\SendMessage;

class Support {
    public function __construct($request)
    {
        $response = new SendMessage([
            'chat_id' => $request['callback_query']['message']['chat']['id'],
            'text' => 'Поддержите нас задонатив на один перечисленных кошельков', 
            'parse_mode' => 'html', 
            'reply_markup' => [
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
                'keyboard' => [
                    [
                        ['text' => 'payme'],
                        ['text' => 'click'],
                    ],
                    [
                        ['text' => 'visa'],
                        ['text' => 'qiwi'],
                    ]
                ]
            ],
            'reply_to_message_id' => null,
        ]);
        $response->send();
    }
}