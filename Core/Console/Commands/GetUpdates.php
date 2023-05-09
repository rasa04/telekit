<?php
namespace Core\Console\Commands;

use Core\Env;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUpdates extends Command
{
    #  TODO
    use Env;

    public static array|string|null $data;

    protected function configure(): void
    {
        $this->setName('getUpdates')->setDescription('Shows updates');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client();
        $response = $client->get("https://api.telegram.org/bot" . self::token() . "/getUpdates", ["verify" => false]);
        self::$data = json_decode($response->getBody()->getContents(), 1);

        $data = self::$data;
        echo "<pre style='background: black; color: white;'>";
        echo '<p style="color: chartreuse; text-align: center; font-size: large"> STATUS: $response["OK"] => ' . ($data['ok'] ? 'true' : 'false') . "</p>";
        echo '<p style="color: chartreuse">RESULT: </p>';

        foreach ($data['result'] as $key => $value) {
            echo "<p id='number-$key' onclick='show($key)' style='color: chartreuse'> [$key] => </p>";
            echo "<div id='el-$key'>";
            var_dump($value);
            echo "</div>";
        }
        echo "</pre>";
        echo
        "<script>
            function show(data)
            {
                if (document.getElementById('el-'+data).style.display  === 'none') {
                    document.getElementById('el-'+data).style.display = 'block'
                    document.getElementById('number-'+data).innerHTML = '['+data+'] =>'
                }
                else {
                    document.getElementById('el-'+data).style.display = 'none'
                    document.getElementById('number-'+data).innerHTML = '['+data+'] ...'
                }
            }
            </script>";


        return Command::SUCCESS;
    }
}