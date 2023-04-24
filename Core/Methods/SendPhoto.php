<?php
namespace Core\Methods;

use Core\Env;
use Core\Storage\Storage;
use Exception;

class SendPhoto extends SendAction
{
    use Env;
    /**
     * Photo caption (may also be used when resending photos by file_id), 0-1024 characters after entities parsing
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
     * Pass True if the photo needs to be covered with a spoiler animation
     */
    public function has_spoiler(string $has_spoiler) : object
    {
        $this->response['has_spoiler'] = $has_spoiler;
        return $this;
    }
    
    /**
     * Photo to send. Pass a file_id as String to send a photo that exists on the Telegram servers (recommended), 
     * pass an HTTP URL as a String for Telegram to get a photo from the Internet, or upload a new photo using multipart/form-data. 
     * The photo must be at most 10 MB in size. The photo's width and height must not exceed 10000 in total. 
     * Width and height ratio must be at most 20.
     */
    public function photo(string $namePath, string $name, string $type = "image/jpg") : object
    {
        $this->response['photo'] = curl_file_create($this->storage_path(). "/img/" . $namePath, $type, $name);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function send(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['chat_id'])) throw new Exception('chat id does not exists');
        if (empty($this->response['photo'])) throw new Exception('photo does not exists');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token() . "/sendPhoto?" . http_build_query($this->response),
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS => json_encode($this->response),
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);    
        $result = curl_exec($curl);
        curl_close($curl);

        //сохраняем то что бот сам отправляет
        if($writeLogFile) $this->writeLogFile(json_decode($result, 1), 'message.txt');
        if($saveDataToJson) Storage::save(json_decode($result, 1), 'data.json');
    }
}