<?php
namespace Triggers;

use Core\Env;
use Core\Methods\SendMessage;
use Core\Controllers;

class NamesPrevalence {
    use Controllers;
    use Env;

    public function __construct($request)
    {
        $response = new SendMessage;

        (preg_match("/^name\s/", strtolower($request['message']['text']))) 
            ? $name = substr($request['message']['text'], 5)
            : $name = substr($request['message']['text'], 7);
        
        $find = $this->client()->get("https://api.nationalize.io/?name=$name", [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
        ]);
        $result = json_decode($find->getBody()->getContents(), true);
            
        // if (!empty($result["country"])) {
            $codes = json_decode(file_get_contents($this->storage_path() . "countries.json"), true);
            foreach ($codes as $key => $value) {
                for ($j = 0; $j < count($result['country']); $j++) { 
                    if ($key == $result['country'][$j]['country_id']) $result['country'][$j]['country_id'] = $value;
                }
            }
            
            $view = "Распространенность имени <b>" . strtoupper($result['name']) . "</b>\n";
            foreach($result["country"] as $val)
            {
                $view .= "страна: <b>" . $val["country_id"] . "</b> | вероятность: <b>" . $val["probability"]*100 . "</b>%\n";
            }

            $response
                ->chat_id($request['message']['chat']['id'])
                ->text($view)
                ->parse_mode()
                ->send();
        // }
    }
}