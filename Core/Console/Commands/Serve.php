<?php
namespace Core\Console\Commands;

use Core\Database\Database;
use Core\Env;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command
{
    use Env;

    public static int $lastUpdate = 0;

    public static bool $handled;

    protected function configure(): void
    {
        $this->setName('serve')->setDescription('Run polling');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $client = new Client();
        $path = "https://api.telegram.org/bot" . self::token() . "/getUpdates";
        $response = json_decode($client->get($path, ["verify" => false])->getBody()->getContents(), 1);

        if (!empty($response['result'])) {
            self::$lastUpdate = $response['result'][0]['update_id'];
        }

        while (true) {
            $path = "https://api.telegram.org/bot" . self::token() . "/getUpdates?offset=" . self::$lastUpdate;
            try {
                $response = json_decode($client->get($path, ["verify" => false])->getBody()->getContents(), 1);
            }
            catch (Exception $e) {
                sleep(2);
                $response = json_decode($client->get($path, ["verify" => false])->getBody()->getContents(), 1);
            }

            // Process the updates

            if (empty($response['result'])) {
                sleep(2);
                continue;
            }
            elseif (isset($GLOBALS['request']) && $response['result'][0]['update_id'] === $GLOBALS['request']['update_id']) {
                self::$lastUpdate+=1;
                continue;
            }
            else {
                $GLOBALS['request'] = $response['result'][0];
                require 'index.php';
            }
            var_dump($GLOBALS['request']);
            self::$lastUpdate+=1;
            echo "\n### SUCCESSFUL HANDLED ###\n";
            sleep(2);
        }
    }
}