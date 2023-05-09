<?php

namespace Core\Console\Commands\Make;

use Core\Env;

trait Paths
{
    use Env;
    public static function samplesPath(): string
    {
        return self::app_path() . "/Core/Console/Samples/";
    }

    public static function responsesPath(): string
    {
        return self::app_path() ."/Responses/";
    }

    public static function databasePath(): string
    {
        return self::app_path() . "/database/";
    }
}