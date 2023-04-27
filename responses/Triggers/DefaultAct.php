<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class DefaultAct extends Trigger {

    public function __construct($request)
    {
        $this->message();
    }
}
