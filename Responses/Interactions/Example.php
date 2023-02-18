<?php
namespace Interactions;

use Core\Methods\AnswerInlineQuery;

class Example {
    public function __construct($request)
    {
        $result = [
            [
                "type" => "article",
                "id" => "0",
                "title" => "Do",
                "description" => "something",
                "input_message_content" => [
                    "message_text" => "результат: <b> OK </b>",
                    "parse_mode" => "HTML"
                ]
            ],
            [
                "type" => "article",
                "id" => "1",
                "title" => "Do 2",
                "description" => "something 2",
                "input_message_content" => [
                    "message_text" => "результат: <b> OK 2 </b>",
                    "parse_mode" => "HTML"
                ]
            ]
        ];

        $response = new AnswerInlineQuery;
        $response->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->send();
    }
}

?>