<?php
namespace Core\Methods;

use Core\Env;
use Exception;

class SendMessage extends SendAction
{
    use Env;

    /**
     * @throws Exception
     */
    public function send(array $headers = [], bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['chat_id'])) throw new Exception('chat id does not exists');
        if (empty($this->response['text'])) throw new Exception('text does not exists');
        
        $response = $this->client()->post("https://api.telegram.org/bot" . $this->token() . "/sendMessage", [
            'headers' => array_merge(["Content-Type" => "application/json"], $headers),
            'verify' => false,
            'json' => $this->response,
        ]);
        $result = $response->getBody()->getContents();

        //сохраняем то что бот сам отправляет
        if($writeLogFile) $this->writeLogFile(json_decode($result, 1));
        if($saveDataToJson) $this->saveDataToJson(json_decode($result, 1));
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
