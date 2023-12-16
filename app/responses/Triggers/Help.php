<?php
namespace Responses\Triggers;

use Core\Entities\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;

class Help extends Trigger implements TriggerInterface{

    public function __construct(array $request, ?Message $message)
    {
        new Start($request, $message);
    }
}
