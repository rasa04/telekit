<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class Settings extends Trigger {
    public function __construct($request)
    {
        $this->sendMessage()
            ->chatId($request['message']['chat']['id'])
            ->parseMode()
            ->text('Settings')
            ->reply_markup([
                'chat_id' => $request['message']['chat']['id'],
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
            ])
            ->send();
    }
}