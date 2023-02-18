<?php
namespace Core;

trait Helpers
{
    public function dd(array $request, int | null $reply_to_message_id = null, bool $disable_notification = true) : void
    {
        $response = [
            'chat_id' => $request['message']['chat']['id'] 
                    ?? $request['callback_query']['message']['chat']['id']
                    ?? $request['callback_query']['from']['id']
                    ?? $request['inline_query']['from']['id'],
            'text' => "<pre>" . json_encode($request) . "</pre>",
            'parse_mode' => 'html', 
            'reply_to_message_id' => $reply_to_message_id,
            'disable_notification' => $disable_notification,
            'allow_sending_without_reply'=> true
        ];
        $response = new \Core\Methods\SendMessage($response);
        $response->send();
        die();
    }

    public function isMessageContainsText(array $message, string $str) : bool
    {
        return (
            isset($message['result']) && str_contains($message['result']['text'], strtolower($str)) ||
            isset($message['message']) && str_contains($message['message']['text'], strtolower($str)) ||
            isset($message['callback_query']) && str_contains($message['callback_query']['message']['text'], strtolower($str))
        ) ? true : false;
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
        $messages = json_decode(file_get_contents(Consts::FILE_DATA), true);
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
        $messages = json_decode(file_get_contents(Consts::FILE_DATA), true);
        foreach ($messages as $message) {
            if (isset($message['result']) && isset($request['message']) && $request['message']['message_id'] == $message['result']['message_id'] + 1) {
                $previousMessage = $message;
            }
        }
        return $previousMessage;
    }

    public function getNewChatMessages(array $request) : array
    { // not sure
        $messages = json_decode(file_get_contents(Consts::FILE_DATA), true);
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
                array_push($belongsChatNewMessages, $message);
            }
            error_reporting(E_ALL);
        }
        return $belongsChatNewMessages;
    }
    
    public function getChatMessages(array $request) : array
    {
        $messages = json_decode(file_get_contents(Consts::FILE_DATA), true);
        $belongsChatMessages = array();
        foreach($messages as $message){
            error_reporting(0);
            if (
                (isset($message['result']) && $message['result']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['result']['chat']['id']) ||
                (isset($message['result']) && $message['result']['chat']['id'] == $request['result']['chat']['id'])
            )
            {
                array_push($belongsChatMessages, $message);
            }
            error_reporting(E_ALL);
        }
        return $belongsChatMessages;
    }

    public function getPreviousChatMessage(array $request) : array
    {
        $messages = json_decode(file_get_contents(Consts::FILE_DATA), true);
        $belongsChatMessages = array();
        foreach($messages as $message){
            error_reporting(0);
            if (
                (isset($message['result']) && $message['result']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['message']['chat']['id']) ||
                (isset($message['message']) && $message['message']['chat']['id'] == $request['result']['chat']['id']) ||
                (isset($message['result']) && $message['result']['chat']['id'] == $request['result']['chat']['id'])
            )
            {
                array_push($belongsChatMessages, $message);
            }
            error_reporting(E_ALL);
        }
        // выбираем предпоследний элемент так как последний это наше сообщение
        if (count($belongsChatMessages)-2 >= 0) {
            $previousChatMessage = $belongsChatMessages[count($belongsChatMessages)-2];
            return $previousChatMessage;
        }
    }

    public function sendWithHttp($method, $textMessage)
    {
        // обычный запрос, без curl
        $urlQuery = 'https://api.telegram.org/bot' . Consts::TOKEN . '/' . $method . "?chat_id=" . Consts::USER_ID . "&text=$textMessage";
        $result = file_get_contents($urlQuery);
        return $result;
    }

}
