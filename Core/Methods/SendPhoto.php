<?php
namespace Core\Methods;

use Core\Consts;
use Core\Helpers;

class SendPhoto
{
    use Helpers;
    private $response;
    private $method = "sendPhoto";

    public function __construct(array $data = [])
    {
        (isset($data['chat_id'])) ?  $this->response['chat_id'] = $data['chat_id'] : throw new \Exception('chat id does not exists');
        if (!empty($data['protect_content'])) $this->response['protect_content'] = $data['protect_content'];
        if (!empty($data['caption'])) $this->response['caption'] = $data['caption'];
    }

    public function chat_id(bool $chat_id = false) : void
    {
        $this->response['chat_id'] = $chat_id;
    }

    public function protect_content(bool $protect_content = false) : void
    {
        $this->response['protect_content'] = $protect_content;
    }
    
    public function caption(string $caption) : void
    {
        $this->response['caption'] = $caption;
    }
    
    public function photo(string $namePath, string $name, string $type = "image/jpg") : void
    {
        $this->response['photo'] = curl_file_create(Consts::STORAGE. "/img/" . $namePath, $type, $name);
    }

    public function send() : void
    {
        if (empty($this->response['photo'])) throw new \Exception('photo does not exists');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/$this->method?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($this->response),
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);    
        $result = curl_exec($curl);
        curl_close($curl);
        //сохраняем то что бот сам отправляет
        $this->writeLogFile(json_decode($result, 1), 'message.txt');
        $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }
}