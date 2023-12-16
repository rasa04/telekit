<?php
namespace Responses\Callbacks;

use Core\Responses\Callback;

class About extends Callback {
    public function __construct($request)
    {
        $this->message()
            ->chatId($request['callback_query']['message']['chat']['id'])
            ->parseMode()
            ->text('<b>Скоро...</b>')
        ->send();
    }
}