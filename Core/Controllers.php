<?php
namespace Core;

/**
 * Тут находятся основные методы для разработки
 */
trait Controllers
{
    public function setIni()
    {
        ini_set('error_reporting', E_ALL);
        ini_set('allow_url_fopen', 1);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    public function getRequest(? bool $writeLogFile = true, ? bool $saveDataToJson = true) : array | null
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (empty($request)) throw new \Exception('[PTB error] Nothing requested', 404);
        if ($writeLogFile) $this->writeLogFile($request, 'message.txt');
        if ($saveDataToJson) $this->saveDataToJson($request, 'data.json');
        return $request;
    }

    public function detectRequest(array $request) : void {
        try {
            if (isset($request['message']['text']))
            {
                if (isset($this->triggers)) {
                    foreach(new \ArrayIterator($this->triggers) as $key => $val)
                        if($key == strtolower($request['message']['text'])) {
                            new $val($request);
                            exit();
                        }
                }
                new \Triggers\DefaultAct($request);
            }
            elseif (isset($request['callback_query']['data']))
            {
                foreach(new \ArrayIterator($this->callbackDatas) as $key => $val)
                    if($key == strtolower($request['callback_query']['data'])) new $val($request);
            }
            elseif (isset($request['inline_query']['query']))
            {
                if (isset($this->inlineQueries)) {
                    foreach(new \ArrayIterator($this->inlineQueries) as $key => $val)
                        if($key == strtolower($request['inline_query']['query'])) {
                            new $val($request);
                            exit(200);
                        }
                }
                new \Interactions\DefaultAct($request);
            }
            elseif (isset($request['game_short_name'])) {
                foreach(new \ArrayIterator($this->games) as $key => $val)
                    if($key == strtolower($request['game_short_name'])) new $val($request);
            }
            else {
                $this->dd($request);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        
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
}



?>