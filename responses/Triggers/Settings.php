<?php
namespace Triggers;

class Settings extends Trigger {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['message']['chat']['id'])
            ->text('Настройки')
            ->send();
    }
}