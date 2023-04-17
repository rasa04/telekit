<?php
namespace Triggers;

use Core\Storage;

class Sample extends Trigger {
    public function __construct($request)
    {
        $this->response()->chat_id($request['message']['chat']['id'])->text("text")->send();
    }
}