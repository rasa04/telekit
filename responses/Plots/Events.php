<?php
namespace Plots;

use Core\Env;
use Core\Methods\SendMessage;
use Exception;

class Events {
    use Env;

    /**
     * @throws Exception
     */
    public function __construct($request)
    {
        $birthdays = json_decode(file_get_contents($this->env("file_birthdays")),  1);
        if (isset($birthdays)) {
            $relatedUserBirthdays = [];
            // выбираем данные принадлежащие пользователью
            foreach($birthdays as $birthday){
                if ($request['callback_query']['from']['id'] == $birthday['from']) {
                    $relatedUserBirthdays[] = $birthday;
                }
            }
            // оформляем для отправки
            $scheduleTemplate = "";
            foreach ($relatedUserBirthdays as $birthday) {
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