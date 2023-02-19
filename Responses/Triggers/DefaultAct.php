<?php
namespace Triggers;

use Core\Consts;
use Core\Methods\SendMessage;

class DefaultAct {

    public function __construct($request)
    {
        $response = new SendMessage;

        if (preg_match("/^name\s/", strtolower($request['message']['text'])) || preg_match("/^имя\s/", strtolower($request['message']['text']))) {
            
            (preg_match("/^name\s/", strtolower($request['message']['text']))) 
                ? $name = substr($request['message']['text'], 5)
                : $name = substr($request['message']['text'], 7);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.nationalize.io/?name=$name",
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            ]);
            $result = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if (!empty($result["country"])) {                
                $codes = json_decode(file_get_contents(Consts::STORAGE . "countries.json"), true);
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
            }else {
                $response
                    ->chat_id($request['message']['chat']['id'])
                    ->text("хз, если честно:/")
                    ->parse_mode()
                    ->send();
            }
        }
        else {
            if ($request["message"]["text"] == "/start@rickbot") {
                $response
                    ->chat_id($request['message']['chat']['id'])
                    ->text('не понел:/')
                    ->parse_mode()
                    ->send();
            }
        }
    }
}


?>