<?php

require_once('./vendor/autoload.php');

use Core\Database\Database;
use Database\models\Chat;
use Dotenv\Dotenv;
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/admin/views';
$cache = __DIR__ . '/admin/cache';

$blade = new BladeOne($views, $cache);

Dotenv::createUnsafeImmutable(__DIR__)->load();
new Database;

echo $blade->setView('index')
    ->share([
        "users_count"=> Chat::query()->count(),
        "superusers_count"=> Chat::query()->where('rights', '>', '0')->count(),
        "chats" => Chat::query()->orderBy('rights', 'desc')->orderBy('id')->get()
    ])
    ->run();