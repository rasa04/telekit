<?php
namespace Triggers;

use Core\Responses\Trigger;

class Help extends Trigger {

    public function __construct($request)
    {
        new Start($request);
    }
}