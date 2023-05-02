<?php
require_once('./vendor/autoload.php');
use Core\Env;
use GuzzleHttp\Client;

class LongPolling
{
    use Env;
    public static int $lastUpdate = 0;

    public static bool $handled;

    public static function handle(): void
    {
        $client = new Client();
        $path = "https://api.telegram.org/bot" . self::token() . "/getUpdates";
        $response = json_decode($client->get($path, ["verify" => false])->getBody()->getContents(), 1);

        if (!empty($response['result'])) {
            self::$lastUpdate = $response['result'][0]['update_id'];
        }

        while (true) {
            $path = "https://api.telegram.org/bot" . self::token() . "/getUpdates?offset=" . self::$lastUpdate;
            $response = json_decode($client->get($path, ["verify" => false])->getBody()->getContents(), 1);

            // Process the updates
            if (!isset($response['result'][0])) {
                sleep(2);
                continue;
            }
            if (!empty($response['result'])) {
                $GLOBALS['request'] = $response['result'][0];
                require 'index.php';
            }
            var_dump($GLOBALS['request']);
            self::$lastUpdate+=1;
            sleep(2);
        }
    }
}

LongPolling::handle();
