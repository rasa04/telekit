<?php
namespace Triggers;

use Core\Methods\SendMessage;

class ChooseBetween {
    public function __construct($request)
    {
        $response = new SendMessage;

        $answers = explode("или", $request['message']['text']);
        if (count($answers) == 1) $answers = explode(" or ", $request['message']['text']);

        $answer = str_replace("?", "", $answers[rand(0, count($answers)-1)]);

        $response
            ->chat_id($request['message']['chat']['id'])
            ->text($answer)
            ->send();
    }
}