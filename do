<?php
require_once('./vendor/autoload.php');

use Core\Console\Kernel\Kernel;
use Dotenv\Dotenv;
Dotenv::createUnsafeImmutable(__DIR__)->load();

new Kernel($argv);
