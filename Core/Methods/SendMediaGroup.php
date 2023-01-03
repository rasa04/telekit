<?php
namespace Core\Methods;

use Core\Consts;
use Core\Helpers;

class SendMediaGroup
{
    use Helpers;
    private $response;
    private $method = "sendMediaGroup";

    public function __construct(array $data)
    {
        (isset($data['chat_id'])) ?  $this->response['chat_id'] = $data['chat_id'] : throw new \Exception('chat id does not exists');
        // Реализация упрошенного создания нескольких медиа файлов 
        for($i = 0; $i < count($data['media']); $i++){
            array_push(
                $this->response['media'],
                ['type' => $data['media'][$i]['type'], 'media' => "attach://" . $data['media'][$i]['name']]
            );
        }
        $this->response['media'] = json_encode($this->response['media']);
        for($i = 0; $i < count($data['media']); $i++){
            $this->response += array($data['media'][$i]['name'] => new \CURLFile( Consts::STORAGE . $data['media'][$i]['path']));
        }
    }
    public function send() : void
    {
        if (empty($this->response['document'])) throw new \Exception('media does not exists');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/$this->method?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $this->response,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        //сохраняем то что бот сам отправляет
        $this->writeLogFile(json_decode($result, 1), 'message.txt');
        $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }
}