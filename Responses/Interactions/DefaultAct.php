<?php
namespace Interactions;

use Core\Methods\AnswerInlineQuery;

class DefaultAct {
    public function __construct($request)
    {   
        if (empty($request['inline_query']['query']))
        {
            $dices = [
                'd4' => 4,
                'd6' => 6,
                'd8' => 8,
                'd10' => 10,
                'd12' => 12,
                'd20' => 20,
                'd100' => 100
            ];
    
            $result = [];
            $i = 0;
            foreach ($dices as $key => $value) {
                array_push($result, [
                    "type" => "article",
                    "id" => $i++,
                    "title" => "Бросить " . "$key",
                    "description" => "Удачной игры!",
                    "input_message_content" => [
                        "message_text" => "<pre>d$value</pre> результат: <b>" . strval(rand(1, $value)) . "</b>",
                        "parse_mode" => "HTML"
                    ]
                ]);
            }

            $response = new AnswerInlineQuery;
            $response->inline_query_id($request['inline_query']['id'])
                ->results($result)
                ->cache_time(1)
                ->is_personal(true)
                ->send(false, false);
        }
        elseif (preg_match("/^d\d/", $request['inline_query']['query']) || preg_match("/^к\d/", $request['inline_query']['query']) )
        {
            $query = $request['inline_query']['query'];

            $query_val = 
                intval((preg_match("/^d\d/", $request['inline_query']['query']) ) 
                    ? substr($request['inline_query']['query'], 1) 
                    : substr($request['inline_query']['query'], 2));
            
            $result = [
                [
                    "type" => "article",
                    "id" => "0",
                    "title" => "Бросить " . "$query",
                    "description" => "Удачной игры!",
                    "input_message_content" => [
                        "message_text" => "<pre>d$query_val</pre> результат: <b>" . strval(rand(1, $query_val)) . "</b>",
                        "parse_mode" => "HTML"
                    ]
                ]
            ];

            $response = new AnswerInlineQuery;
            $response->inline_query_id($request['inline_query']['id'])
                ->results($result)
                ->cache_time(1)
                ->is_personal(true)
                ->send(false, false);
        }
    }
}

?>