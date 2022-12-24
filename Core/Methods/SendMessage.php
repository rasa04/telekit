<?php
namespace Core\Methods;

use Core\Methods;

class SendMessage extends Methods{
    public $Consts;

    public static $data;
    public static $chat_id;
    public static $text;
    public static $parse_mode;
    public static $reply_to_message_id;
    
    public function __construct(array $data)
    {
        $this->Consts = new \Core\Consts();

        (isset($data['chat_id'])) ? self::$chat_id = $data['chat_id'] : self::$chat_id = $this->Consts::CHAT_ID;

        if (!empty($data['text'])) {
            self::$text = $data['text'];
        }
        if (!empty($data['parse_mode'])) {
            self::$parse_mode = $data['parse_mode'];
        }
        if (!empty($data['reply_to_message_id'])) {
            self::$reply_to_message_id = $data['reply_to_message_id'];
        }
        
        self::$data = [
            "chat_id" => self::$chat_id,
            "text" => self::$text,
            "parse_mode" => self::$parse_mode,
            "reply_to_message_id" => self::$reply_to_message_id,
            "reply_markup" => $data['reply_markup']
        ];
        
        return $data;
    }
}

