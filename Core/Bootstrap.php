<?php
namespace Core;

class Bootstrap
{
    use Controllers;

    private array $request;

    private array $triggers;
    
    private array $callbackDatas;
    
    private array $inlineQueries;

    private array $games;

    public function handle(bool $writeLogFile = true, bool $saveDataToJson = true)
    {
        // SET INI
        $this->setIni();

        // GET REQUEST
        $this->request = $this->getRequest($writeLogFile, $saveDataToJson);
        
        // DETECT PLOT
        $this->detectRequest($this->request);
    }

    public function triggers(array $triggers) : object {
        $this->triggers = $triggers;
        return $this;
    }

    public function callbackDatas(array $callbackDatas) : object {
        $this->callbackDatas = $callbackDatas;
        return $this;
    }
    
    /**
     * You can trace and response to all queries first in Interactions\DefaultAct class
     * Be careful that the new classes for processing inline Queries do not contradict each other
     * use php regex without specifing any delimiters
     * @param $inlineQUeries an associative array
     * @return object context
     */
    public function inlineQueries(array $inlineQueries) : object {
        $this->inlineQueries = $inlineQueries;
        return $this;
    }

    public function games(array $games) : object {
        $this->games = $games;
        return $this;
    }

}
