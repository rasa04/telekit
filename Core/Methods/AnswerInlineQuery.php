<?php
namespace Core\Methods;

use Core\Controllers;
use Core\Env;
use Core\Storage\Storage;
use Exception;

class AnswerInlineQuery
{
    use Controllers;
    use Env;

    private array $response;

    /**
     * @throws Exception
     */
    public function send(bool $writeLogFile = true, bool $saveDataToJson = true) : void
    {
        if (empty($this->response['inline_query_id'])) throw new Exception('inline_query_id does not exists');
        if (empty($this->response['results'])) throw new Exception('inline query result does not exists');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.telegram.org/bot' . $this->token() . "/answerInlineQuery",
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_POSTFIELDS => http_build_query($this->response),
            CURLOPT_SSL_VERIFYPEER => 0,
        ]);
        $result = curl_exec($curl);
        curl_close($curl);

        //сохраняем то что бот сам отправляет
        if($writeLogFile) $this->writeLogFile(json_decode($result, 1), 'message.txt');
        if($saveDataToJson) Storage::save(json_decode($result, 1), 'data.json');
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
        $this->response['results'] = json_encode($results);
        return $this;
    }

    /**
     * The maximum amount of time in seconds that the result of the inline query may be cached on the server. Defaults to 300.
     */
    public function cache_time(int $cache_time) : object
    {
        $this->response['cache_time'] = json_encode($cache_time);
        return $this;
    }

    /**
     * Pass True if results may be cached on the server side only for the user that sent the query. 
     * By default, results may be returned to any user who sends the same query
     */
    public function is_personal(bool $is_personal) : object
    {
        $this->response['is_personal'] = json_encode($is_personal);
        return $this;
    }

}
