<?php
namespace Interactions;

class DefaultAct extends Interaction {

    public string $request_query;

    public function __construct($request)
    {   
        $this->request_query = $request['inline_query']['query'];

        if (empty($this->request_query))
        {
            $dices = [
                'd20' => 20,
                'd4' => 4,
                'd100' => 100
            ];
    
            $result = [];
            $i = 0;
            foreach ($dices as $key => $value) {
                $result[] = [
                    "type" => "article",
                    "id" => $i++,
                    "title" => "Бросить " . "$key",
                    "description" => "Удачной игры!",
                    "input_message_content" => [
                        "message_text" => "<code>d$value</code> <b>результат:</b> <code>" . rand(1, $value) . "</code>",
                        "parse_mode" => "HTML"
                    ]
                ];
            }

            $this->response()->inline_query_id($request['inline_query']['id'])
                ->results($result)
                ->cache_time(1)
                ->is_personal(true)
                ->send(false, false);
        }
    }
}
