<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class DefaultAct extends Trigger {

    public function __construct($request)
    {
        if ($request['message']['chat']['id'] === $request['message']['from']['id']) new OpenAI($request);
    }
}
