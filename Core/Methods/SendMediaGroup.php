<?php
namespace Core\Methods;

use Core\Methods;

class SendMediaGroup extends Methods {

    public $Consts;
    public static $chat_id;
    public static $media = [];
    public static $data;

    public function __construct(array $data){
        $this->Consts = new \Core\Consts();
        (isset($data['chat_id'])) ?  self::$chat_id = $data['chat_id'] : self::$chat_id = $this->Consts::CHAT_ID;

        // Реализация упрошенного создания нескольких медиа файлов 
        for($i = 0; $i < count($data['media']); $i++){
            array_push(
                self::$media,
                ['type' => $data['media'][$i]['type'], 'media' => "attach://" . $data['media'][$i]['name']]
            );
        }
        
        self::$data = [
            'chat_id' => $this->Consts::CHAT_ID,
            'media' => json_encode(self::$media)
        ];

        for($i = 0; $i < count($data['media']); $i++){

            self::$data += array($data['media'][$i]['name'] => new \CURLFile(__DIR__ . "/../../storage/img/" . $data['media'][$i]['path']));
        }

    }
}