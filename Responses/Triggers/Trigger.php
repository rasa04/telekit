<?php

namespace Triggers;

use Core\Methods\SendMessage;
use Core\Controllers;
use Core\Env;

class Trigger
{
    use Controllers;
    use Env;

    public function response(): SendMessage
    {
        return new SendMessage;
    }
}