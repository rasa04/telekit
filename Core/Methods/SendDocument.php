<?php
namespace Core\Methods;

use Core\Consts;

class SendDocument
{
    use PropertiesTrait;
    use \Core\Controllers;

    private $response;
    
    /**
     * Document caption (may also be used when resending documents by file_id), 0-1024 characters after entities parsing
     */
    public function caption(string $caption) : object
    {
        $this->response['caption'] = $caption;
        return $this;
    }
        
    /**
     * A JSON-serialized list of special entities that appear in the caption, which can be specified instead of parse_mode
     */
    public function caption_entities(string $caption_entities) : object
    {
        $this->response['caption_entities'] = $caption_entities;
        return $this;
    }

    /**
     * A JSON-serialized list of special entities that appear in the caption, which can be specified instead of parse_mode
     */
    public function disable_content_type_detection(bool $disable_content_type_detection) : object
    {
        $this->response['disable_content_type_detection'] = $disable_content_type_detection;
        return $this;
    }

    /**
     * Thumbnail of the file sent; can be ignored if thumbnail generation for the file is supported server-side. 
     * The thumbnail should be in JPEG format and less than 200 kB in size. A thumbnail's width and height should not exceed 320. 
     * Ignored if the file is not uploaded using multipart/form-data. 
     * Thumbnails can't be reused and can be only uploaded as a new file, so you can pass “attach://<file_attach_name>” 
     * if the thumbnail was uploaded using multipart/form-data under <file_attach_name>.
     */
    public function thumb(string $thumb) : object
    {
        // BETA
        $this->response['thumb'] = $thumb;
        return $this;
    }
    
    /**
     * File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended),
     * pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     */
    public function document(string $namePath, string $name, string $type = "image/jpg") : object
    {
        $this->response['document'] = curl_file_create(Consts::STORAGE . "/docs/" . $namePath, $type, $name);
        return $this;
    }
    
    public function send(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['chat_id'])) throw new \Exception('chat id does not exists');
        if (empty($this->response['document'])) throw new \Exception('document does not exists');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/sendDocument?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($this->response),
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        //сохраняем то что бот сам отправляет
        if($writeLogFile == true) $this->writeLogFile(json_decode($result, 1), 'message.txt');
        if($saveDataToJson == true) $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }
}