<?php
require_once('./vendor/autoload.php');

use Core\Env;
use GuzzleHttp\Client;

class GetUpdates
{
    use Env;

    public static array|string|null $data;

    public static function get(): void
    {
        $client = new Client();
        $response = $client->get("https://api.telegram.org/bot" . self::token() . "/getUpdates", ["verify" => false]);
        self::$data = json_decode($response->getBody()->getContents(), 1);
    }

    public static function show(): void
    {
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
    }
}

GetUpdates::get();
GetUpdates::show();
