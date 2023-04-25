<?php
require_once('./vendor/autoload.php');

use Core\Console\Kernel;
use Dotenv\Dotenv;

Dotenv::createUnsafeImmutable(__DIR__)->load();

try {
    new Kernel($argv);
} catch (\Doctrine\DBAL\Exception $e) {
    var_dump($e);
}

