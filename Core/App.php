<?php
namespace Core;

use Core\Storage\Storage;
use Core\Validator\ErrorHandler;
use Responses\Interactions\DefaultAct as InteractionDefault;
use Responses\Triggers\DefaultAct as TriggerDefault;

class App
{
    use Controllers;
    use Env;

    private static array $triggers;
    private static array $callbackData;
    private static array $inlineQueries;
    private static array $games;
    private static array $voices;
    private static array $invoices;

    public function handle(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        date_default_timezone_set($this->time_zone());
        ini_set('error_reporting', E_ALL);
        ini_set('allow_url_fopen', 1);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

        $this->setRequest();
        if ($writeLogFile) $this->writeLogFile($GLOBALS['request']);
        if ($saveDataToJson) Storage::save($GLOBALS['request']);
        $this->matchingResponse();
    }

    public static function triggers(array $triggers): App
    {
        static::$triggers = $triggers;
        return new static;
    }

    public static function callbacks(array $callbackData): App {
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
    public static function inlineQueries(array $inlineQueries): App {
        static::$inlineQueries = $inlineQueries;
        return new static;
    }

    public static function games(array $games): App {
        static::$games = $games;
        return new static;
    }

    public static function voices(array $voices): App {
        static::$voices = $voices;
        return new static;
    }
    public static function invoices(array $invoices): App
    {
        static::$invoices = $invoices;
        return new static;
    }

    private function setRequest(): void
    {
        if (isset($GLOBALS['request'])) return;

        $request = json_decode(file_get_contents('php://input'), true);
        if (empty($request)) new ErrorHandler('Nothing requested');
        $GLOBALS['request'] = $request;
    }
    
    private function matchingResponse() : void {
        if (isset($GLOBALS['request']['message']['text'])) $this->matchTriggers();
        elseif (isset($GLOBALS['request']['callback_query']['data'])) $this->matchCallbackQueries();
        elseif (isset($GLOBALS['request']['inline_query']['query'])) $this->matchInlineQueries();
        elseif (isset($GLOBALS['request']['game_short_name'])) $this->matchGames();
        elseif (isset($GLOBALS['request']['message']['voice'])) $this->matchVoices();
        elseif (isset($GLOBALS['request']['pre_checkout_query'])) $this->matchInvoices();

        if     (isset($GLOBALS['request']['message']['text']))       new TriggerDefault($GLOBALS['request']);
        elseif (isset($GLOBALS['request']['inline_query']['query'])) new InteractionDefault($GLOBALS['request']);
    }

    private function matchTriggers(): void
    {
        foreach(static::$triggers as $key => $val) {
            if (!preg_match("#$key#", strtolower($GLOBALS['request']['message']['text']))) return;
            new $val($GLOBALS['request']);
        }
    }

    private function matchInlineQueries(): void
    {
        foreach(static::$inlineQueries as $key => $val) {
            if (!preg_match("#$key#", strtolower($GLOBALS['request']['inline_query']['query']))) return;
            new $val($GLOBALS['request']);
        }
    }

    private function matchCallbackQueries(): void
    {
        foreach(static::$callbackData as $key => $val) {
            if (!preg_match("#$key#", strtolower($GLOBALS['request']['callback_query']['data']))) return;
            new $val($GLOBALS['request']);
        }
    }

    private function matchGames(): void
    {
//        $iterator = new \ArrayIterator(static::$games);
//        $data_value = $GLOBALS['request']['game_short_name'];
    }

    private function matchVoices(): void
    {
        foreach(static::$voices as $voiceHandler) {
            new $voiceHandler($GLOBALS['request']);
        }
    }

    private function matchInvoices(): void
    {
        foreach (static::$invoices as $key => $class) {
            if ($key == $GLOBALS['request']['pre_checkout_query']['invoice_payload']) new $class;
        }
    }
}
