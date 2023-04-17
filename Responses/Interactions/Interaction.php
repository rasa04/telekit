<?php

namespace Interactions;

use Core\Methods\AnswerInlineQuery;
use Core\Controllers;
use Core\Env;

class Interaction
{
    use Controllers;
    use Env;

    public function response(): AnswerInlineQuery
    {
        return new AnswerInlineQuery;
    }
}