<?php

namespace Core\Validator;
class ErrorHandler
{
    public function __construct($message)
    {
        echo "TELEKIT ERROR: $message";
    }
}