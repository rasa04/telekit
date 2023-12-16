<?php
require_once('./vendor/autoload.php');
use Core\App;
use Core\Database\Database;
use Dotenv\Dotenv;

use Middlewares\BaseMiddleware;
use Responses\Invoices\SubscriptionForMonth;
use Responses\Triggers\{DefaultAct, Start, Help, NamesPrevalence, ChooseBetween, OpenAI, Settings, Subscription};
use Responses\Triggers\Admin\GetIP;
use Responses\Callbacks\{About, Support, Settings as SettingsPlot};
use Responses\Inlines\Dices;

Dotenv::createUnsafeImmutable(__DIR__)->load();
new Database;

App::middlewares([
        BaseMiddleware::class
    ])
    ->triggers([
        "rasa ip" => GetIP::class,
        "/start$" => Start::class,
        "/start@rickbot$" => Start::class,
        "/help$" => Help::class,
        "/help@rickbot$" => Help::class,
        "/settings$" => Settings::class,
        "/settings@settings$" => Settings::class,
        "^(name|имя)\s" => NamesPrevalence::class,
        "\s(or|или)\s" => ChooseBetween::class,
        "/subscription" => Subscription::class,
        "^(openai|Openai|gpt|Gpt|ии|рик|Рик|rick|Rick)(\s|,\s)" => OpenAI::class,
    ], default: DefaultAct::class)
    ->voices([
        OpenAI::class
    ])
    ->callbacks([
        "about" => About::class,
        "support" => Support::class,
        "settings" => SettingsPlot::class,
    ])
    ->inlineQueries([
        "^(d|к)\d" => Dices::class,
        "^([1-9]|[1-9][0-9])(d|к)\d" => Dices::class,
    ], )
    ->games([
        'example' => null
    ])
    ->invoices([
        "Подписка на месяц" => SubscriptionForMonth::class
    ])
    ->handle();
