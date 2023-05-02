<?php
namespace Core;

use Core\Methods\SendMessage;
use Core\Storage\Storage;
use Database\models\Group;
use Database\models\User;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\NoReturn;

trait Controllers
{
    use Env;
    public function writeLogFile(string | array $str, string $file = "message.txt", bool $overwrite = false) : void
    {
        $log_file_name = $this->storage_path() . "$file";
        $now = date("Y-m-d H:i:s");
        if ($overwrite) file_put_contents($log_file_name, '');
        file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
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
                Storage::save($request);
            }
            
            // забираем название файла
            $newFilePath = $this->storage_path() . "img/" . explode("/", $fileUrl)[1];
            // сохраняем файл на серсер
            file_put_contents($newFilePath, file_get_contents($photoPathTG));
        }
        return $request;
    }

    public function client() : object
    {
        return new Client();
    }

    public function authorized(): bool
    {
        $group = Group::where('group_id', $GLOBALS['request']['message']['chat']['id'])->first('rights');
        $user = User::where('user_id', $GLOBALS['request']['message']['chat']['id'])->first('role');
        if ($group) {
            return $group->toArray()['rights'] == 'pro';
        } else if ($user) {
            return $user->toArray()['role'] == 'pro';
        } else {
            return false;
        }
    }

    public function chat_gpt($messages): string
    {
        $response = $this->client()->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->gpt_token(),
            ],
            'json' => [
                "model" => "gpt-3.5-turbo",
                "messages" => $messages,
            ],
            'verify' => false,
        ]);

        $result = json_decode($response->getBody()->getContents(), true)['choices'][0]['message']['content'];

        return (strlen($result) < 4000) ? $result : substr($result, 0, 4000) . "...";
    }

    #[NoReturn] public function dd(string $data, bool $disable_notification = true, bool $allow_sending_without_reply = true) : void
    {
        (new SendMessage)->chat_id($GLOBALS['request']['message']['chat']['id']
                ?? $GLOBALS['request']['callback_query']['message']['chat']['id']
                ?? $GLOBALS['request']['callback_query']['from']['id']
                ?? $GLOBALS['request']['inline_query']['from']['id']
                ?? null)
            ->text($data)
            ->disable_notification($disable_notification)
            ->allow_sending_without_reply($allow_sending_without_reply)
            ->send();
        die();
    }
}
