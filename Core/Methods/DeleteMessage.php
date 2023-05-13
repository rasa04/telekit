<?php
namespace Core\Methods;

use Core\Env;

class DeleteMessage extends SendAction
{
    use Env;

    public function delete(int $message_id = null) : void
    {
        $this->client()->post("https://api.telegram.org/bot" . $this->token() . "/deleteMessage", [
            'headers' => array_merge(["Content-Type" => "application/json"]),
            'verify' => false,
            'json' => [
                'chat_id' => $GLOBALS['request']['message']['chat']['id'],
                'message_id' => $message_id ?? $GLOBALS['request']['message']['message_id']
            ],
        ]);
    }
}
