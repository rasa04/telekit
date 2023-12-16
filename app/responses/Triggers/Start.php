<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class Start extends Trigger {
    public function __construct($request)
    {
        $text = "<b>Приветствую! Общайся со мной через текст или голосовые сообщения!</b>\n"
        ."<b>МОИ ВОЗМОЖНОСТИ</b>\n\n"
        ."Команда <code>@rickbot</code> что бы бросать игровые кости, если хотите бросить несколько укажите в виде 5d6 или 5к6\n\n"
        ."Можно задавать вопросы по типу 'поспать или ну его нафиг?' - бот ответит \n\n"
        ."Команды <code>name</code> и <code>имя</code> предназначены для просмотра распространенности имени в разных странах\n\n"
        ."<b>Если добавляете в чат предоставьте админку что бы с ботом было удобнее работать</b>\n\n";

        $this->sendMessage()
            ->chatId($request['message']['chat']['id'])
            ->text($text)
            ->parseMode()
            ->reply_markup([
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'About',
                            'callback_data' => 'about',
                        ],
                        [
                            'text' => 'Support',
                            'callback_data' => 'support',
                        ]
                    ],
                    [
                        [
                            'text' => 'Settings',
                            'callback_data' => 'settings',
                        ]
                    ]
                ]
            ])
        ->send();
    }
}