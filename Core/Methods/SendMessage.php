<?php
namespace Core\Methods;
use \Core\Consts;
use Core\Helpers;

class SendMessage
{
    use Helpers;
    private $response;
    private $method = "sendMessage";
    
    public function __construct(array $data)
    {
        (isset($data['chat_id'])) ? $this->response['chat_id'] = $data['chat_id'] : throw new \Exception('chat id does not exists');
        (isset($data['text'])) ? $this->response['text'] = $data['text'] : throw new \Exception('chat id does not exists');
        if (isset($data['text'])) $this->response['text'] = $data['text'];
        if (isset($data['parse_mode']))  $this->response['parse_mode'] = $data['parse_mode'];
        if (isset($data['reply_to_message_id']))  $this->response['reply_to_message_id'] = $data['reply_to_message_id'];
        if (isset($data['reply_markup'])) $this->response['reply_markup'] = $data['reply_markup'];
    }

    public function send(array $headers = []) : void
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/$this->method?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($this->response),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers),
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        //сохраняем то что бот сам отправляет
        $this->writeLogFile(json_decode($result, 1), 'message.txt');
        $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }
}