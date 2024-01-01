<?php
namespace Responses\Triggers;

use Core\API\Types\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;

class ChooseBetween extends Trigger implements TriggerInterface {
    public function __construct(array $request, ?Message $message)
    {
        $answers = explode("или", $request['message']['text']);
        if (count($answers) == 1) $answers = explode(" or ", $request['message']['text']);

        $answer = str_replace("?", "", $answers[rand(0, count($answers)-1)]);

        $this->replyMessage($answer);
    }
}