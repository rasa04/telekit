<?php
namespace Responses\Triggers;

use Core\API\Types\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;

class DefaultAct extends Trigger implements TriggerInterface {

    public function __construct(array $request, ?Message $message)
    {
        if ($request['message']['chat']['id'] === $request['message']['from']['id']) new OpenAI($request, $message);
    }
}
