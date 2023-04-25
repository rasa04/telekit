<?php
namespace Triggers;

class Help extends Trigger {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['message']['chat']['id'])
            ->text('Помощь')
            ->send();
    }
}