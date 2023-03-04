<?php
namespace Triggers;

use Core\Consts;
use Core\Methods\SendMessage;

class DefaultAct {

    public function __construct($request)
    {
        $response = new SendMessage;
    }
}


?>