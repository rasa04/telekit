<?php
require_once('./vendor/autoload.php');

use Triggers\{GetApi, Start, Help, Settings};
use Plots\{SetBirthday, SetEvent, Functions, Support, Events};
new class {
    use Core\Controllers;

    private array $request;

    private array $triggers = [
        "/start" => Start::class,
        "api" => GetApi::class,
        "/help" => Help::class,
        "/settings" => Settings::class,
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
     */
    private array $inlineQueries = [

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