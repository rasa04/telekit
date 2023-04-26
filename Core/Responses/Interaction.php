<?php

namespace Core\Responses;

use Core\Controllers;
use Core\Env;
use Core\Methods\AnswerInlineQuery;

class Interaction
{
    use Controllers;
    use Env;

    public function response(): AnswerInlineQuery
    {
        return new AnswerInlineQuery;
    }
}