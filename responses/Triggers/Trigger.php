<?php

namespace Triggers;

use Core\Methods\SendMessage;
use Core\Methods\SendMediaGroup;
use Core\Methods\SendDocument;
use Core\Methods\SendPhoto;
use Core\Controllers;
use Core\Env;

class Trigger
{
    use Controllers;
    use Env;

    public function message(): SendMessage
    {
        return new SendMessage;
    }
    public function photo(): SendPhoto
    {
        return new SendPhoto;
    }
    public function document(): SendDocument
    {
        return new SendDocument;
    }
    public function mediaGroup(): SendMediaGroup
    {
        return new SendMediaGroup;
    }
}