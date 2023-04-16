<?php
namespace Core;

/**
 * Тут находятся вспомогательные методы для разработки
 */
trait Helpers
{
    use Env;
    public function isMessageContainsText(array $message, string $str) : bool
    {
        return
            isset($message['result']) && str_contains($message['result']['text'], strtolower($str)) ||
            isset($message['message']) && str_contains($message['message']['text'], strtolower($str)) ||
            isset($message['callback_query']) && str_contains($message['callback_query']['message']['text'], strtolower($str));
    }

    public function getMessage(int $message_id, string $type = "ALL") : array | bool
    {
        /**
         * @param int $message_id
         * id of searching message
         * @param string $type
         * type == "ALL" sorts result types and message types
         * type == message sorts message types
         * type == result sorts result types
         */
        $messages = json_decode(file_get_contents($this->file_data()), true);
        foreach($messages as $message){
            if ($type == "ALL") {
                if(isset($message['message']) && $message['message']['message_id'] == $message_id){
                    return $message; 
                }else if(isset($message['result']) && $message['result']['message_id'] == $message_id){
                    return $message; 
                }
            }else{
                if(isset($message[$type]) && $message[$type]['message_id'] == $message_id){
                    return $message; 
                }
            }
        }
        return false;
    }

    public function getPreviousMessage(array $request) : array
    {
        $messages = json_decode(file_get_contents($this->file_data()), true);
        foreach ($messages as $message) {
            if (isset($message['result']) && isset($request['message']) && $request['message']['message_id'] == $message['result']['message_id'] + 1) {
                $previousMessage = $message;
            }
        }
        return $previousMessage ?? [];
    }

    public function getNewChatMessages(array $request) : array
    { // not sure
        $messages = json_decode(file_get_contents($this->file_data()), true);
        $belongsChatNewMessages = array();
        foreach($messages as $message){
            error_reporting(0);
            if (
                (
                    isset($message['message']) &&
                    (
                        ($message['message']['chat']['id'] == $request['message']['chat']['id']) ||
                        ($message['message']['chat']['id'] == $request['result']['chat']['id'])
                    )
                )
                ||
                (
                    isset($message['result']) &&
                    (
                        ($message['result']['date'] > $request['message']['date']) ||
                        ($message['result']['date'] > $request['result']['date'])
                    )
                )
            )
            {
                $belongsChatNewMessages[] = $message;
            }
            error_reporting(E_ALL);
        }
        return $belongsChatNewMessages;
    }
    
    public function getChatMessages(array $request) : array
    {
        $messages = json_decode(file_get_contents($this->file_data()), true);
        $belongsChatMessages = [];
        foreach($messages as $message){
            error_reporting(0);
            if (
                (isset($message['result']) && $message['result']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['result']['chat']['id']) ||
                (isset($message['result']) && $message['result']['chat']['id'] == $request['result']['chat']['id'])
            )
            {
                $belongsChatMessages[] = $message;
            }
            error_reporting(E_ALL);
        }
        return $belongsChatMessages;
    }

    public function getPreviousChatMessage(array $request) : array
    {
        $messages = json_decode(file_get_contents($this->file_data()), true);
        $belongsChatMessages = array();
        foreach($messages as $message){
            error_reporting(0);
            if (
                (
                    isset($message['result']) && ($message['result']['chat']['id'] == $request['message']['chat']['id']) ||
                    $message['result']['chat']['id'] == $request['result']['chat']['id']
                )
                ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['result']['chat']['id'])
            )
            {
                $belongsChatMessages[] = $message;
            }
            error_reporting(E_ALL);
        }
        // выбираем предпоследний элемент так как последний это наше сообщение
        return (count($belongsChatMessages)-2 >= 0) ? $belongsChatMessages[count($belongsChatMessages)-2] : [];
    }

    public function sendWithHttp(string $method, string $textMessage): false|string
    {
        // обычный запрос, без curl
        return file_get_contents('https://api.telegram.org/bot' . $this->token() . "/$method?chat_id=" . $this->default_user_id() . "&text=$textMessage");
    }

}
