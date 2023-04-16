<?php
namespace Core;

use Core\Methods\SendMessage;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\NoReturn;

/**
 * Тут находятся основные методы для разработки
 */
trait Controllers
{
    use Env;
    public function writeLogFile(string | array $str, string $file = "message.txt", bool $overwrite = false) : void
    {
        $log_file_name = $this->storage() . "$file";
        $now = date("Y-m-d H:i:s");
        if ($overwrite) file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
    }

    public function saveDataToJson(array $data, string $file_name = "data.json", bool $overwrite = false) : void
    {
        $file_link = $this->storage() . "$file_name";
        $file_content = json_decode(file_get_contents($file_link)) ?? [];
        (!$overwrite) ? array_push($file_content, $data) : $file_content = $data;
        file_put_contents($file_link, json_encode($file_content));
    }

    public function saveFile(bool $withLog = false) : array
    {
        $request = json_decode(file_get_contents('php://input'), true);

        // RECEIVE FILE
        if (!empty($request["message"]["photo"])) {
            $file = [
                "file_id" => $request["message"]["photo"][3]["file_id"],
            ];
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token() . "/getFile?" . http_build_query($file),
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
            $photoPathTG = "https://api.telegram.org/file/bot" . $this->token() . "/" . $fileUrl;

            if ($withLog) {
                $this->writeLogFile($photoPathTG, $withLog, true);
                $this->saveDataToJson($request);
            }
            
            // забираем название файла
            $newFilePath = $this->storage() . "img/" . explode("/", $fileUrl)[1];
            // сохраняем файл на серсер
            file_put_contents($newFilePath, file_get_contents($photoPathTG));
        }
        return $request;
    }

    public function client() : object
    {
        return new Client();
    }

    #[NoReturn] public function dd(array $request, bool $disable_notification = true, bool $allow_sending_without_reply = true) : void
    {
        $response = new SendMessage;
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
