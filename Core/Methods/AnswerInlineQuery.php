<?php
namespace Core\Methods;

use \Core\Consts;

class AnswerInlineQuery
{
    use \Core\Controllers;
    
    private array $response;

    public function send(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['inline_query_id'])) throw new \Exception('inline_query_id does not exists');
        if (empty($this->response['results'])) throw new \Exception('inline query result does not exists');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . Consts::TOKEN . "/answerInlineQuery",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($this->response),
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        //сохраняем то что бот сам отправляет
        if($writeLogFile == true) $this->writeLogFile(json_decode($result, 1), 'message.txt');
        if($saveDataToJson == true) $this->saveDataToJson(json_decode($result, 1), 'data.json');
    }

    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     */
    public function inline_query_id(string $inline_query_id) : object
    {
        $this->response['inline_query_id'] = $inline_query_id;
        return $this;
    }

    /**
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     */
    public function results(array | string $results) : object
    {
        $this->response['results'] = $results;
        return $this;
    }

}
