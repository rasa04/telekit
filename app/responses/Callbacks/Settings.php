<?php
namespace Responses\Callbacks;

use Core\Responses\Callback;

class Settings extends Callback{
    public function __construct($request)
    {
        $this->message()
            ->chatId($request['callback_query']['message']['chat']['id'])
            ->parseMode()
            ->text('<b>Скоро...</b>')
            ->reply_markup([
                'chat_id' => $request['callback_query']['message']['chat']['id'],
                'text' => 'Функции',
                'parse_mode' => 'html',
                'reply_to_message_id' => null,
                'reply_markup' => [
                    'one_time_keyboard' => true,
                    'resize_keyboard' => true,
                    'keyboard' => [
                        [
                            ['text' => 'Languages'],
                            ['text' => 'Profile'],
                        ],
                        [
                            ['text' => 'Upgrade'],
                        ]
                    ]
                ],
            ])->send();
    }
}