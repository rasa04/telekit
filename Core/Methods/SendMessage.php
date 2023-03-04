<?php
namespace Core\Methods;

use \Core\Consts;

class SendMessage extends SendAction
{
    public function send(array $headers = [], bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['chat_id'])) throw new \Exception('chat id does not exists');
        if (empty($this->response['text'])) throw new \Exception('text does not exists');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/sendMessage?" . http_build_query($this->response),
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
        if($writeLogFile == true) $this->writeLogFile(json_decode($result, 1), 'message.txt');
        if($saveDataToJson == true) $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }

    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     */
    public function text(string $text) : object
    {
        $this->response['text'] = $text;
        return $this;
    }

    /**
     * A JSON-serialized list of special entities that appear in message text, which can be specified instead of parse_mode
     */
    public function entities(array $entities) : object
    {
        // BETA
        $this->response['entities'] = $entities;
        return $this;
    }

    /**
     * Disables link previews for links in this message
     */
    public function disable_web_page_preview(bool $disable_web_page_preview) : object
    {
        $this->response['disable_web_page_preview'] = $disable_web_page_preview;
        return $this;
    }

}
