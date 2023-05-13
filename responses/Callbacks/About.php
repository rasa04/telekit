<?php
namespace Responses\Callbacks;

use Core\Responses\Callback;

class About extends Callback {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['callback_query']['message']['chat']['id'])
            ->parse_mode()
            ->text('<b>Скоро...</b>')
        ->send();
    }
}