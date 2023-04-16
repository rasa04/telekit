<?php
namespace Core;

use Dotenv\Dotenv;
use Exception;

class Bootstrap
{
    use Controllers;

    // for messages
    private array $triggers;
    // for inline keyboards
    private array $callbackData;
    // for inline queries
    private array $inlineQueries;
    // for games
    private array $games;

    /**
     * @throws Exception
     */

    public function __construct()
    {
        date_default_timezone_set('Asia/Tashkent');
    }

    public function handle(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        // SET INI
        $this->setIni();

        // GET REQUEST
        $request = $this->getRequest($writeLogFile, $saveDataToJson);
        
        // DETECT PLOT
        $this->detectRequest($request);
    }

    public function triggers(array $triggers) : object {
        $this->triggers = $triggers;
        return $this;
    }

    public function callbackDatas(array $callbackData) : object {
        $this->callbackData = $callbackData;
        return $this;
    }
    
    /**
     * You can trace and response to all queries first in Interactions\DefaultAct class
     * Be careful that the new classes for processing inline Queries do not contradict each other
     * use php regex without specifying any delimiters
     * @param $inlineQueries : array
     * @return $this : object context
     */
    public function inlineQueries(array $inlineQueries) : object {
        $this->inlineQueries = $inlineQueries;
        return $this;
    }

    public function games(array $games) : object {
        $this->games = $games;
        return $this;
    }

    public function setIni() : void
    {
        ini_set('error_reporting', E_ALL);
        ini_set('allow_url_fopen', 1);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    /**
     * @throws Exception
     */
    public function getRequest(bool $writeLogFile = true, bool $saveDataToJson = true) : array | null
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (empty($request)) throw new Exception('[PTB error] Nothing requested', 404);
        if ($writeLogFile) $this->writeLogFile($request, 'message.txt');
        if ($saveDataToJson) $this->saveDataToJson($request, 'data.json');
        return $request;
    }

    
    public function detectRequest(array $request) : void {
        // DETECT REQUEST TYPE
        $data_value = $request['message']['text']
                    ?? $request['callback_query']['data']
                    ?? $request['inline_query']['query']
                    ?? $request['game_short_name']
                    ?? null;

        // CREATE ITERATOR FOR ALL REGISTERED RESPONSES
        if (isset($request['message']['text'])) $iterator = new \ArrayIterator($this->triggers);
        elseif (isset($request['callback_query']['data'])) $iterator = new \ArrayIterator($this->callbackData);
        elseif (isset($request['inline_query']['query'])) $iterator = new \ArrayIterator($this->inlineQueries);
        elseif (isset($request['game_short_name'])) $iterator = new \ArrayIterator($this->games);
        // else $this->dd($request);

        // EXECUTE MATCHED RESPONSE
        if (isset($iterator)) {
            foreach($iterator as $key => $val)
                // HANDLE A REQUEST WHICH STARTS WITH $KEY
                if(preg_match("#$key#", strtolower($data_value))) {new $val($request); exit();}
        }
        
        // DEFAULT HANDLERS
        if (isset($request['message']['text'])) new \Triggers\DefaultAct($request);
        elseif (isset($request['inline_query']['query'])) new \Interactions\DefaultAct($request);
    }
}
