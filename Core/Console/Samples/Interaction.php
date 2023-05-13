<?php
namespace Responses\Inlines;

use Core\Responses\Interaction;

class Sample extends Interaction {

    public string $request_query;

    public function __construct($request)
    {
        $this->request_query = $request['inline_query']['query'];

        if (!empty($this->request_query)) die();

        $result[] = [
            "type" => "article",
            "id" => 0,
            "title" => "title",
            "description" => "Description",
            "input_message_content" => [
                "message_text" => "Text message",
                "parse_mode" => "HTML"
            ]
        ];

        $this->response()->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->cache_time(1)
            ->is_personal(true)
            ->send(false, false);
    }
}
