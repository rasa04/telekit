<?php
namespace Core\Console\Samples;

use Core\Responses\Trigger;

class Sample extends Trigger {
    public function __construct($request)
    {
        $this->response()->chat_id($request['message']['chat']['id'])->text("text")->send();
    }
}