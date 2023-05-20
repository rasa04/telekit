<?php

use Database\models\Chat;
use eftec\bladeone\BladeOne;

$views = __DIR__ . '/admin/views';
$cache = __DIR__ . '/admin/cache';

$blade = new BladeOne($views, $cache);

echo $blade->setView('index')
    ->share([
        "users_count"=> Chat::count(),
        "superusers_count"=> Chat::where('rights', '>', '0')->count(),
        "chats" => Chat::orderBy('rights', 'desc')->orderBy('id')->get()
    ])
    ->run();