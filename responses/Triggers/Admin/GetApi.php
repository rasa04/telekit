<?php
namespace Triggers\Admin;
use Triggers\Trigger;

class GetApi extends Trigger {
    public function __construct($request)
    {
        $result = $this->client()->get('https://api.ipify.org', [
            'query' => [
                'format' => 'json'
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'verify' => false
        ])->getBody()->getContents();

        $this->message()
            ->chat_id($request['message']['chat']['id'])
            ->text($result)
            ->parse_mode()
            ->send();
    }
}