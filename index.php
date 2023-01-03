<?php
require_once('./vendor/autoload.php');

use Core\Templates\{Start, SetBirthday, SetEvent, Functions, Support, Events};

class RequestHandler {
    use Core\Helpers;
    use Core\IniSets;
    public function __construct()
    {
        $this->handle();
    }

    private $commands = [
        ["/start", Start::class]
    ];

    private $callbackDatas = [
        ["Назначить день рождение", SetBirthday::class],
        ["Назначить особый день", SetEvent::class],
        ["Функции", Functions::class],
        ["Поддержка", Support::class],
        ["События", Events::class],
    ];

    private function handle(){
        /* SET INI */
        $this->setIni();
        /* LOADING COMMANDS */
        $commands = $this->commands;
        $callbackDatas = $this->callbackDatas;
        
        /* GETTING REQUEST */
        $request = $this->getRequest();

        /* SEND RESPONSE */
        if (isset($request['message'])) {
            foreach($commands as $command){
                if ($command[0] == strtolower($request['message']['text'])) new $command[1]($request);
            }
        }
        else if (isset($request['callback_query'])) {
            foreach($callbackDatas as $callbackData){
                if($callbackData[0] == strtolower($request['callback_query']['data'])) new $callbackData[1]($request);
            }
        }
        else {
            throw new Exception('не понятный тип запроса', 404);
        }
    }
}
// EXECUTE
new RequestHandler();
?>