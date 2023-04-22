<?php

namespace Core\Console\Kernel;

use Core\Console\Kernel\Commands\Make;
use Core\Console\Kernel\Commands\Database;
use Core\Console\Kernel\Commands\Send;
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
    ];

    /**
     * @throws Exception
     */
    public function __construct($argv)
    {
        $options = getopt(short_options: $this->short_options, long_options: $this->long_options);

        if ($argv[1] === "--send") new Send($options, $argv);
        elseif ($argv[1] === "--make") new Make($options, $argv);
        elseif ($argv[1] === "--database") new Database($options, $argv);
        else echo "Unknown command: " . $argv[1];
    }
}