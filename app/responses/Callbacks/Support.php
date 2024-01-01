<?php
namespace Responses\Callbacks;

use Core\Responses\Callback;

class Support extends Callback {
    public function __construct($request)
    {
        $this->message()
            ->chatId($request['callback_query']['message']['chat']['id'])
            ->parseMode()
            ->text('<b>Скоро...</b>')
            ->replyMarkup([
                'chat_id' => $request['callback_query']['message']['chat']['id'],
                'text' => '<b>Скоро...<b>',
                'parse_mode' => 'html',
                'reply_to_message_id' => null,
            ]);
    }
}