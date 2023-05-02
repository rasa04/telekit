<?php

namespace Core\Console;

use Core\Console\Commands\DatabaseCommands;
use Core\Console\Commands\Make;
use Core\Console\Commands\Send;
use Core\Validator\ErrorHandler;

class Kernel
{
    private string $short_options = "";
    private array $long_options = [
        "send",
        "to::",
        "message::",
        "trigger::",
        "interaction::",
        "database::",
        "model::",
        "connect::",
        "get::",
        "table::",
        "tables::",
        "params::",
        "migrate::",
        "migration::",
        "fresh::",
        "showTable::",
        "column::",
        "driver::",
    ];

    public function __construct($argv)
    {
        $options = getopt(short_options: $this->short_options, long_options: $this->long_options);

        if ($argv[1] === "--send") new Send($options, $argv);
        elseif ($argv[1] === "--make") new Make($options, $argv);
        elseif ($argv[1] === "--database") new DatabaseCommands($options, $argv);
        elseif ($argv[1] === "tinker") require "tinker.php";
        elseif ($argv[1] === "serve") require "Commands/LongPolling.php";
        elseif ($argv[1] === "getUpdates") exec("php -S localhost:8000 " . __DIR__ . "/Commands/GetUpdates.php");
        else new ErrorHandler("Unknown command: " . $argv[1]);
    }
}