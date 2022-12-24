<?php
namespace Core\Methods;

use Core\Methods;

class SendDocument extends Methods {

    public $Consts;
    public static $data;

    public static $chat_id;
    public static $protect_content;
    public static $caption;
    public static $document;

    /**
     * 
     * Конструктор принимает массив с ключами
     * protect_content, caption, document, namePath, type, name
     * можно еще передать chat_id, но если если не передать то просто будет использоваться
     * константа из области Core\Consts
     * 
     */

    public function __construct(array $data = [])
    {
        $this->Consts = new \Core\Consts();
        (isset($data['chat_id'])) ?  self::$chat_id = $data['chat_id'] : self::$chat_id = $this->Consts::CHAT_ID;
        if (!empty($data['protect_content'])) {
            self::$protect_content = $data['protect_content'];
        }
        if (!empty($data['caption'])) {
            self::$caption = $data['caption'];
        }
        if (isset($data['namePath']) && isset($data['type']) && isset($data['name'])) {
            self::$document = curl_file_create(__DIR__ . "/../../storage/img/" . $data['namePath'], $data['type'], $data['name']);
        }

        self::$data = [
            "chat_id" => self::$chat_id,
            "protect_content" => self::$protect_content,
            "caption" => self::$caption,
            "document" => self::$document
        ];
        // var_dump(self::$data['document']->name); exit();
    }

    public static function protect_content($protect_content)
    {
        self::$protect_content = $protect_content;
        self::$data['protect_content'] = self::$protect_content;
    }

    public static function caption($caption)
    {
        self::$caption = $caption;
        self::$data['caption'] = self::$caption;
    }

    public static function document($namePath, $name, $type = "image/jpg")
    {
        self::$document = curl_file_create(__DIR__ . "/../../storage/img/" . $namePath, $type, $name);
        self::$data['document'] = self::$document;
    }
}