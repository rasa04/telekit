<?php

namespace Responses\Triggers;

use Core\Entities\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;
use Database\models\Country;

class NamesPrevalence extends Trigger implements TriggerInterface{

    public function __construct(array $request, ?Message $message)
    {
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

        $countries = Country::all(['code', 'name'])->toArray();

        foreach ($countries as $country) {
            for ($i = 0; $i < count($result['country']); $i++) {
                if ($country["code"] === $result['country'][$i]['country_id']) {
                    $result['country'][$i]['country_id'] = $country["name"];
                }
            }
        }

        $view = "Распространенность имени <b>" . strtoupper($result['name']) . "</b>\n";
        foreach($result["country"] as $val) {
            $view .= "страна: <b>${val["country_id"]}</b> | вероятность: <b>" . $val["probability"]*100 . "</b>%\n";
        }

        $this->sendMessage()->chatId($request['message']['chat']['id'])->text($view)->parseMode()->send();
    }
}
