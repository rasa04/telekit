<?php
namespace Interactions;

use Core\Methods\AnswerInlineQuery;

class Rolls {
    public function __construct($request)
    {
        $result = [
            "type" => "article",
            "id" => "0",
            "title" => "test title",
            "input_message_content" => [
                "message_text" => strval(rand(1, 20)),
                "parse_mode" => "HTML"
            ]
        ];

        $response = new AnswerInlineQuery;
        $response->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->send();
    }
}

?>