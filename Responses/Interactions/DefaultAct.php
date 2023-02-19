<?php
namespace Interactions;

use Core\Methods\AnswerInlineQuery;

class DefaultAct {
    public function __construct($request)
    {   
        if (empty($request['inline_query']['query']))
        {
            $dices = [
                'd20' => 20,
                'd12' => 12,
                'd6' => 6,
                'd4' => 4,
                'd8' => 8,
                'd10' => 10,
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
                        "message_text" => "<code>d$value</code> <b>результат:</b> <code>" . strval(rand(1, $value)) . "</code>",
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
                        "message_text" => "<code>d$query_val</code> <b>результат:</b> <code>" . strval(rand(1, $query_val)) . "</code>",
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
        elseif (preg_match("/^[123456789]d\d/", $request['inline_query']['query']) || preg_match("/^[123456789]к\d/", $request['inline_query']['query']))
        {
            $query = $request['inline_query']['query'];

            $query_val = 
                intval((preg_match("/^[123456789]d\d/", $request['inline_query']['query'])) 
                    ? substr($request['inline_query']['query'], 2) 
                    : substr($request['inline_query']['query'], 3));

            $dices = substr($query, 0, 1);
            
            $view = "[";
            $sum = 0;
            for ($i=0; $i < $dices; $i++) {
                $temp = rand(1, $query_val);
                $sum += $temp;
                $view .= ($i < $dices - 1) ? strval($temp) . "," :  strval($temp);
            }

            $view .= "]";

            $result = [
                [
                    "type" => "article",
                    "id" => "0",
                    "title" => "Бросить " . "$query",
                    "description" => "Удачной игры!",
                    "input_message_content" => [
                        "message_text" => "<code>$dices" . "d$query_val</code> <b>результат: $sum</b> <code>$view</code>",
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