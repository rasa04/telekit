<?php
namespace Core\Methods;

use Core\Env;
use Core\Storage\Storage;
use CURLFile;
use Exception;

class SendMediaGroup extends SendAction
{
    use Env;
    /**
     * A JSON-serialized array describing messages to be sent, must include 2-10 items
     */
    public function media($data): static
    {
        // Реализация упрошенного создания нескольких медиа файлов 
        for($i = 0; $i < count($data['media']); $i++){
            $this->response['media'][] = ['type' => $data['media'][$i]['type'], 'media' => "attach://" . $data['media'][$i]['name']];
        }
        $this->response['media'] = json_encode($this->response['media']);
        for($i = 0; $i < count($data['media']); $i++){
            $this->response += array($data['media'][$i]['name'] => new CURLFile( $this->storage_path() . $data['media'][$i]['path']));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function send(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['chat_id'])) throw new Exception('chat id does not exists');
        if (empty($this->response['document'])) throw new Exception('media does not exists');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token() . "/sendMediaGroup?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => $this->response,
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        
        //сохраняем то что бот сам отправляет
        if($writeLogFile) $this->writeLogFile(json_decode($result, 1));
        if($saveDataToJson) Storage::save(json_decode($result, 1));
    }
}