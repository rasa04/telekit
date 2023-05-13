<?php
namespace Responses\Triggers\Admin;
use Core\Responses\Trigger;

class GetIP extends Trigger {
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

        $this->reply_message("<code>".json_decode($result, 1)['ip']."</code>");
    }
}