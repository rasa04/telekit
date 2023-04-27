<?php

namespace Core\Responses;

use Core\Controllers;
use Core\Env;
use Core\Methods\AnswerInlineQuery;

class Interaction
{
    use Controllers;
    use Env;

    public function answerInlineQuery(): AnswerInlineQuery
    {
        return new AnswerInlineQuery;
    }
}