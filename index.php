<?php
require_once('./vendor/autoload.php');

use Triggers\{Start, Help, Settings};
use Plots\{SetBirthday, SetEvent, Functions, Support, Events};

new class {
    use Core\Controllers;

    private array $request;

    private array $triggers = [
        "/start" => Start::class,
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

    private array $inlineQueries = [
        'example' => null
    ];

    private array $games = [
        'example' => null
    ];

    public function __construct()
    {
        // SET INI
        $this->setIni();

        // GET REQUEST
        $this->request = $this->getRequest();
        
        // DETECT PLOT
        $this->detectRequest($this->request);
    }
}
?>