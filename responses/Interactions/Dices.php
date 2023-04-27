<?php
namespace Responses\Interactions;

use Core\Responses\Interaction;
use Exception;

class Dices extends Interaction {
    
    public string $request_query;

    /**
     * @throws Exception
     */
    public function __construct($request)
    {
        $this->request_query = $request['inline_query']['query'];

        if (preg_match("/^(d|к)\d/", $request['inline_query']['query']))
        {
            $dice_value = (int) substr($this->request_query, (preg_match("/^d\d/", $this->request_query) ) ?  1 :  2);
            if ($dice_value > 1000000000) exit();
            $message_text = "<code>d$dice_value</code> <b>результат:</b> <code>" . random_int(1, $dice_value) . "</code>";
        }
        else
        {
            $less_then_ten = preg_match("/^[1-9](d|к)\d/", $this->request_query);
            $is_type_latin = preg_match("/d\d/", $this->request_query);

            if ($less_then_ten) {
                $amount_of_dices = (int) substr($this->request_query, 0, 1);
                $dice_value = (int) substr($this->request_query, ($is_type_latin) ? 2 : 3);
            }
            else {
                $amount_of_dices = (int) substr($this->request_query, 0, 2);
                $dice_value = (int) substr($this->request_query,  ($is_type_latin) ? 3 : 4);
            }
            if ($dice_value > 1000000000) exit();

            $sum = 0;
            if ($less_then_ten) {
                $view = "[";
                for ($i = 0; $i < $amount_of_dices; $i++) {
                    $temp = random_int(1, $dice_value);
                    $sum += $temp;
                    $view .= $temp;
                    if ($i < $amount_of_dices - 1) $view .=  ",";
                }
                $view .= "]";
            }else {
                $number_of_tuples = floor($amount_of_dices / 10);
                
                $view = "\n";
                for ($j = 0; $j < $number_of_tuples; $j++) { 
                    $view .= "[";
                    for ($i=0; $i < 10; $i++) {
                        $temp = random_int(1, $dice_value);
                        $sum += $temp;
                        $view .= "$temp";
                        if($i != 9) $view .= ",";
                    }
                    $view .= "]\n";
                }
                if ($amount_of_dices % 10 != 0)
                {
                    $view .= "[";
                    for ($i=0; $i < $amount_of_dices % 10; $i++) {
                        $temp = random_int(1, $dice_value);
                        $sum += $temp;
                        $view .= "$temp";
                        if($i != $amount_of_dices % 10 - 1) $view .= ",";
                    }
                    $view .= "]";
                }
            }

            $message_text = ($amount_of_dices == 1) 
                ? "<code>d$dice_value</code> <b>результат: $sum</b>"
                : "<code>$amount_of_dices" . "d$dice_value</code> <b>результат: $sum</b> <code>$view</code>";
        }

        $result = [
            [
                "type" => "article",
                "id" => "0",
                "title" => "Бросить $this->request_query",
                "description" => "Удачной игры!",
                "input_message_content" => [
                    "message_text" => $message_text,
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
