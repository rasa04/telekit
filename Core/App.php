<?php
namespace Core;

use ArrayIterator;
use Core\Storage\Storage;
use Core\Validator\ErrorHandler;
use Exception;
use Interactions\DefaultAct as InteractionDefault;
use Triggers\DefaultAct as TriggerDefault;

class App
{
    use Controllers;
    use Env;

    /**
     * Messages
     */
    private static array $triggers;

    /**
     * Inline keyboards
     */
    private static array $callbackData;

    /**
     * Inline queries
     */
    private static array $inlineQueries;

    /**
     * Games
     */
    private static array $games;

    public function __construct()
    {

    }

    /**
     * @throws Exception
     */
    public function handle(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        date_default_timezone_set($this->time_zone());
        static::setIni();

        try {
            $request = $this->getRequest($writeLogFile, $saveDataToJson);
            $this->detectRequest($request);
        } catch (Exception $e) {
            new ErrorHandler($e);
        }
    }

    public static function triggers(array $triggers) : App
    {
        static::$triggers = $triggers;
        return new static;
    }

    public function callbacks(array $callbackData) : App {
        static::$callbackData = $callbackData;
        return new static;
    }
    
    /**
     * You can trace and response to all queries first in Interactions\DefaultAct class
     * Be careful that the new classes for processing inline Queries do not contradict each other
     * use php regex without specifying any delimiters
     * @param $inlineQueries : array
     * @return App : object context
     */
    public function inlineQueries(array $inlineQueries) : App {
        static::$inlineQueries = $inlineQueries;
        return new static;
    }

    public function games(array $games) : object {
        static::$games = $games;
        return new static;
    }

    public static function setIni() : void
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
        if (empty($request)) new ErrorHandler('Nothing requested');
        if ($writeLogFile) $this->writeLogFile($request);
        if ($saveDataToJson) Storage::save($request);
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
        if     (isset($request['message']['text']))        $iterator = new ArrayIterator(static::$triggers);
        elseif (isset($request['callback_query']['data'])) $iterator = new ArrayIterator(static::$callbackData);
        elseif (isset($request['inline_query']['query']))  $iterator = new ArrayIterator(static::$inlineQueries);
        elseif (isset($request['game_short_name']))        $iterator = new ArrayIterator(static::$games);

        // EXECUTE MATCHED RESPONSE
        if (isset($iterator)) {
            foreach($iterator as $key => $val)
                // HANDLE A REQUEST WHICH STARTS WITH $KEY
                if(preg_match("#$key#", strtolower($data_value))) {new $val($request); exit();}
        }
        
        // DEFAULT HANDLERS
        if (isset($request['message']['text'])) new TriggerDefault($request);
        elseif (isset($request['inline_query']['query'])) new InteractionDefault($request);
    }
}
