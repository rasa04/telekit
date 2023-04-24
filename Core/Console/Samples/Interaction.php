<?php
namespace Core\Console\Samples;

use Core\Console\Samples\Interaction;

class Sample extends Interaction {
    public function __construct($request)
    {
        $result = [
            [
                "type" => "article",
                "id" => "0",
                "title" => "сообщение",
                "description" => "Удачной игры!",
                "input_message_content" => [
                    "message_text" => "message text",
                    "parse_mode" => "HTML"
                ]
            ]
        ];

        $this->response()->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->cache_time(1)
            ->is_personal(true)
            ->send(false, false);
    }
}
