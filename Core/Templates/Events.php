<?php
namespace Core\Templates;

use Core\Consts;
use Core\Methods\SendMessage;

class Events {
    public function __construct($request)
    {
        $birthdays = json_decode(file_get_contents(Consts::FILE_BIRTHDAYS),  1);
        if (isset($birthdays)) {
            $reletedUserBirthdays = [];
            // выбираем данные принадлежащие пользователью
            foreach($birthdays as $birthday){
                if ($request['callback_query']['from']['id'] == $birthday['from']) {
                    array_push($reletedUserBirthdays, $birthday);
                }
            }
            // оформляем для отправки
            $scheduleTemplate = "";
            foreach ($reletedUserBirthdays as $birthday) {
                $scheduleTemplate .= "У <b>" . $birthday["who"] . "</b> день рождение в " . $birthday["date"] . "!\n";
            }
            //подготовка к методу отправки
            $response = new SendMessage([
                'chat_id' => $request['callback_query']['message']['chat']['id'],
                'text' => $scheduleTemplate,
                'parse_mode' => 'html', 
                'reply_to_message_id' => null,
            ]);
        }else {
            $response = new SendMessage([
                'chat_id' => $request['callback_query']['message']['chat']['id'],
                'text' => "У вас пока нету событий",
                'parse_mode' => 'html', 
                'reply_to_message_id' => null,
            ]);
        }
        $response->send();
    }
}