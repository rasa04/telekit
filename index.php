<?php
require_once('./vendor/autoload.php');

use Interactions\Dices;
use Triggers\Admin\GetApi;
use Triggers\{Start, Help, NamesPrevalence, FirstOrSecond, OpenAi, Settings};
use Plots\{SetBirthday, SetEvent, Functions, Support, Events};
new class {
    use Core\Controllers;

    private array $request;

    private array $triggers = [
        "^(openai|Openai|gpt|Gpt|ии|рик|Рик)\s" => OpenAi::class,
        "rasa api" => GetApi::class,
        "/start$" => Start::class,
        "/start@rickbot$" => Start::class,
        "/help$" => Help::class,
        "/help@rickbot$" => Help::class,
        "/settings$" => Settings::class,
        "/settings@settings$" => Settings::class,
        "^(name|имя)\s" => NamesPrevalence::class,
        "\s(or|или)\s" => FirstOrSecond::class,
    ];
    
    private array $callbackDatas = [
        "Назначить день рождение" => SetBirthday::class,
        "Назначить особый день" => SetEvent::class,
        "Функции" => Functions::class,
        "Поддержка" => Support::class,
        "События" => Events::class,
    ];
    
    /**
     * You can trace and response to all queries first in Interactions\DefaultAct class
     * Be careful that the new classes for processing inline Queries do not contradict each other
     * 
     * in default use php regex without specifing any delimiters
     */
    private array $inlineQueries = [
        "^(d|к)\d" => Dices::class,
        "^([1-9]|[1-9][0-9])(d|к)\d" => Dices::class,
    ];

    private array $games = [
        'example' => null
    ];

    public function __construct()
    {
        // SET INI
        $this->setIni();

        // GET REQUEST
        $this->request = $this->getRequest(false, false);
        
        // DETECT PLOT
        $this->detectRequest($this->request);
    }
}
?>