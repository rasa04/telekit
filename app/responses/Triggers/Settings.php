<?php
namespace Responses\Triggers;

use Core\API\Types\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;

class Settings extends Trigger implements TriggerInterface {
    public function __construct(array $request, ?Message $message)
    {
        $this->sendMessage()
            ->chatId($request['message']['chat']['id'])
            ->parseMode()
            ->text('Settings')
            ->replyMarkup([
                'chat_id' => $request['message']['chat']['id'],
                'text' => 'Функции',
                'parse_mode' => 'html',
                'reply_to_message_id' => null,
                'replyMarkup' => [
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
