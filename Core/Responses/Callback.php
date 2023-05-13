<?php

namespace Core\Responses;

use Core\Controllers;
use Core\Env;
use Core\Methods\SendMessage;

class Callback
{
    use Controllers;
    use Env;

    public function message(): SendMessage
    {
        return new SendMessage();
    }
}