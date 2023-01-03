<?php
namespace Core\Templates;

use Core\Methods\SendMessage;

class Start {
    public function __construct($request)
    {
        $response = new SendMessage([
            'chat_id' => $request['message']['chat']['id'],
            'text' => 'Выберите опцию', 
            'parse_mode' => 'html', 
            'reply_to_message_id' => null,
            'reply_markup' => [
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Назначить день рождение',
                            'callback_data' => 'Назначить день рождение',
                        ],
                        [
                            'text' => 'Назначить особый день',
                            'callback_data' => 'Назначить особый день',
                        ]
                    ],
                    [
                        [
                            'text' => 'События',
                            'callback_data' => 'События',
                        ],
                        [
                            'text' => 'Функции',
                            'callback_data' => 'Функции',
                        ],
                        [
                            'text' => 'Поддержка',
                            'callback_data' => 'Поддержка',
                        ]
                    ]
                ]
            ]
        ]);
        $response->send();
    }
}