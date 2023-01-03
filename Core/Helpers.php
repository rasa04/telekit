<?php
namespace Core;

use Exception;

trait Helpers
{
    public function dd(array $request) : void
    {
        $response = [
            'chat_id' => $request['message']['chat']['id'],
            'text' => "<pre>" . json_encode($request) . "</pre>",
            'parse_mode' => 'html', 
            'reply_to_message_id' => null,
        ];
        $response = new \Core\Methods\SendMessage($response);
        $response->send();
        die();
    }

    public function writeLogFile(string | array $str, string $file = "message.txt", bool $clear = false) : void
    {
        $log_file_name = Consts::STORAGE . "/$file";
        $now = date("Y-m-d H:i:s");
        if ($clear == false) {
            file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
        }else{
            file_put_contents($log_file_name, '');
            file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
        }
    }

    public function saveDataToJson(array $data, string $file = "data.json", bool $clear = false) : void
    {
        $storageFile = Consts::STORAGE . "/$file";
        if ($clear == false)
        {
            $inp = file_get_contents($storageFile);
            $tempArray = json_decode($inp);
            (empty($tempArray)) ? $tempArray = array() : $tempArray;
            array_push($tempArray, $data);
            $jsonData = json_encode($tempArray);
            file_put_contents($storageFile, $jsonData);
        }
        else
        {
            $jsonData = json_encode($data);
            file_put_contents($storageFile, $jsonData);
        }
    }

    public function getRequest(bool $withErrorIfEmpty = false) : array | null
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if ($request != null) {
            $this->writeLogFile($request, 'message.txt');
            $this->saveDataToJson($request, 'data.json');
        }
        return ($withErrorIfEmpty == false) ? $request : throw new Exception('[PTB error] Nothing requested', 404);
    }

    public function isMessageContainsText(array $message, string $str) : bool
    {
        if (
            isset($message['result']) && str_contains($message['result']['text'], strtolower($str)) ||
            isset($message['message']) && str_contains($message['message']['text'], strtolower($str)) ||
            isset($message['callback_query']) && str_contains($message['callback_query']['message']['text'], strtolower($str))
        ) return true;
        else return false;
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

    public function saveFile($withLog = null) : array
    {
        $Consts = new \Core\Consts();
        $request = json_decode(file_get_contents('php://input'), true);

        /**
         * Обрабатываем отправленные файлы
         */
        if (!empty($request["message"]["photo"])) {
            $file_id = $request["message"]["photo"][3]["file_id"];
            $file = [
                "file_id" => $file_id,
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/getFile?" . http_build_query($file),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_SSL_VERIFYPEER => 0,
                // CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers),
            ]);
            
            $result = curl_exec($curl);
            curl_close($curl);

            // записываем ответ в формате PHP массива
            $dataResult = json_decode($result, true);
            // записываем URL необходимого изображения
            $fileUrl = $dataResult["result"]["file_path"];
            // формируем полный URL до файла
            $photoPathTG = "https://api.telegram.org/file/bot" . $Consts::TOKEN . "/" . $fileUrl;

            if ($withLog != null) {
                $this->writeLogFile($photoPathTG, $withLog, true);
                $this->saveDataToJson($request, 'data.json');
            }
            
            // забираем название файла
            $newFilePath = Consts::STORAGE . "/img/" . explode("/", $fileUrl)[1];
            // сохраняем файл на серсер
            file_put_contents($newFilePath, file_get_contents($photoPathTG));
        }

        return $request;
    }

    public function sendWithHttp($method, $textMessage)
    {
        // обычный запрос, без curl
        $urlQuery = 'https://api.telegram.org/bot' . Consts::TOKEN . '/' . $method . "?chat_id=" . Consts::USER_ID . "&text=$textMessage";
        $result = file_get_contents($urlQuery);
        return $result;
    }

}
