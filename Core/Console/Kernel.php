<?php

namespace Core\Console;

use Core\Console\Commands\DatabaseCommands;
use Core\Console\Commands\Make;
use Core\Console\Commands\Send;
use Core\Validator\ErrorHandler;
use Doctrine\DBAL\Exception;

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
        "connect::",
        "get::",
        "table::",
        "tables::",
        "parameters::",
        "migrate::",
        "migration::",
        "fresh::",
        "showTable::",
        "column::"
    ];

    /**
     * @throws Exception
     */
    public function __construct($argv)
    {
        $options = getopt(short_options: $this->short_options, long_options: $this->long_options);

        if ($argv[1] === "--send") new Send($options, $argv);
        elseif ($argv[1] === "--make") new Make($options, $argv);
        elseif ($argv[1] === "--database") new DatabaseCommands($options, $argv);
        elseif ($argv[1] === "tinker") require "tinker";
        else new ErrorHandler("Unknown command: " . $argv[1]);
    }
}