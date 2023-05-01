<?php

namespace Core\Validator;
use JetBrains\PhpStorm\NoReturn;

class ErrorHandler
{
    #[NoReturn] public function __construct($message)
    {
        echo "TELEKIT ERROR: $message";
        die();
    }
}