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

    public function getRequest(bool $writeLogFile = true, bool $saveDataToJson = true) : array | null
    {
        $request = json_decode(file_get_contents('php://input'), true);
        if (empty($request)) throw new \Exception('[PTB error] Nothing requested', 404);
        if ($writeLogFile) $this->writeLogFile($request, 'message.txt');
        if ($saveDataToJson) $this->saveDataToJson($request, 'data.json');
        return $request;
    }

    public function detectRequest(array $request) : void {
        // DETECT REQUEST TYPE
        $data_value = $request['message']['text']
                    ?? $request['callback_query']['data']
                    ?? $request['inline_query']['query']
                    ?? $request['game_short_name']
                    ?? null;

        // CREATE ITERATOR FOR ALL REGISTRATED RESPONSES
        if (isset($request['message']['text'])) $iterator = new \ArrayIterator($this->triggers);
        elseif (isset($request['callback_query']['data'])) $iterator = new \ArrayIterator($this->callbackDatas);
        elseif (isset($request['inline_query']['query'])) $iterator = new \ArrayIterator($this->inlineQueries);
        elseif (isset($request['game_short_name'])) $iterator = new \ArrayIterator($this->games);
        else $this->dd($request);

        // EXECUTE MATCHED RESPONSE
        if (isset($iterator)) {
            foreach($iterator as $key => $val)
                // HANDLE A REQUEST WHICH STARTS WITH $KEY
                if(preg_match("#$key#", strtolower($data_value))) {new $val($request); exit();}
        }
        
        // DEFAULT HANDLERS
        if (isset($request['message']['text'])) new \Triggers\DefaultAct($request);
        elseif (isset($request['inline_query']['query'])) new \Interactions\DefaultAct($request);
    }

    public function writeLogFile(string | array $str, string $file = "message.txt", bool $overwrite = false) : void
    {
        $log_file_name = Consts::STORAGE . "$file";
        $now = date("Y-m-d H:i:s");
        if ($overwrite) file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
    }

    public function saveDataToJson(array $data, string $file_name = "data.json", bool $overwrite = false) : void
    {
        $file_link = Consts::STORAGE . "$file_name";
        $file_content = json_decode(file_get_contents($file_link)) ?? [];
        if (!$overwrite) array_push($file_content, $data);
        file_put_contents($file_link, json_encode($file_content));
    }

    public function saveFile(bool $withLog = false) : array
    {
        $Consts = new \Core\Consts();
        $request = json_decode(file_get_contents('php://input'), true);

        // RECIEVE FILE
        if (!empty($request["message"]["photo"])) {
            $file = [
                "file_id" => $request["message"]["photo"][3]["file_id"],
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

            if ($withLog) {
                $this->writeLogFile($photoPathTG, $withLog, true);
                $this->saveDataToJson($request, 'data.json');
            }
            
            // забираем название файла
            $newFilePath = Consts::STORAGE . "img/" . explode("/", $fileUrl)[1];
            // сохраняем файл на серсер
            file_put_contents($newFilePath, file_get_contents($photoPathTG));
        }
        return $request;
    }

    public function dd(array $request, bool $disable_notification = true, bool $allow_sending_without_reply = true) : void
    {
        $response = new \Core\Methods\SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'] 
                ?? $request['callback_query']['message']['chat']['id']
                ?? $request['callback_query']['from']['id']
                ?? $request['inline_query']['from']['id']
                ?? null)
            ->text("<code>" . json_encode($request) . "</code>")
            ->disable_notification($disable_notification)
            ->allow_sending_without_reply($allow_sending_without_reply)
            ->send();
        die();
    }
}

?>