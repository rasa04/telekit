<?php
require_once('./vendor/autoload.php');
use Core\App;
use Core\Database\Database;
use Dotenv\Dotenv;

use Responses\Triggers\{Start, Help, NamesPrevalence, ChooseBetween, OpenAi, Settings};
use Responses\Triggers\Admin\GetApi;
use Responses\Plots\{About, Support, Settings as SettingsPlot};
use Responses\Interactions\Dices;

Dotenv::createUnsafeImmutable(__DIR__)->load();
new Database;

App::triggers([
        "^(openai|Openai|gpt|Gpt|ии|рик|Рик)(\s|,\s)" => OpenAi::class,
        "rasa api" => GetApi::class,
        "/start$" => Start::class,
        "/start@rickbot$" => Start::class,
        "/help$" => Help::class,
        "/help@rickbot$" => Help::class,
        "/settings$" => Settings::class,
        "/settings@settings$" => Settings::class,
        "^(name|имя)\s" => NamesPrevalence::class,
        "\s(or|или)\s" => ChooseBetween::class,
    ])
    ->callbacks([
        "about" => About::class,
        "support" => Support::class,
        "settings" => SettingsPlot::class,
    ])
    ->inlineQueries([
        "^(d|к)\d" => Dices::class,
        "^([1-9]|[1-9][0-9])(d|к)\d" => Dices::class,
    ])
    ->games([
        'example' => null
    ])
    ->handle();
