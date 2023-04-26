<?php
namespace Plots;

use Core\Responses\Plot;

class About extends Plot {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['callback_query']['message']['chat']['id'])
            ->parse_mode()
            ->text('<b>Скоро...</b>')
        ->send();
    }
}