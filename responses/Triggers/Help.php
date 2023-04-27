<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;
use Triggers\Start;

class Help extends Trigger {

    public function __construct($request)
    {
        new Start($request);
    }
}