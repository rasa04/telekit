<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class ChooseBetween extends Trigger {
    public function __construct($request)
    {
        $answers = explode("или", $request['message']['text']);
        if (count($answers) == 1) $answers = explode(" or ", $request['message']['text']);

        $answer = str_replace("?", "", $answers[rand(0, count($answers)-1)]);

        $this->replyMessage($answer);
    }
}