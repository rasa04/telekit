<?php
namespace Triggers;

use Core\Responses\Trigger;

class Settings extends Trigger {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['message']['chat']['id'])
            ->parse_mode()
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