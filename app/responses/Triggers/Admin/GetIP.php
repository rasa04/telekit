<?php
namespace Responses\Triggers\Admin;
use Core\API\Types\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;

class GetIP extends Trigger implements TriggerInterface {
    public function __construct(array $request, ?Message $message)
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

        $this->replyMessage("<code>".json_decode($result, 1)['ip']."</code>");
    }
}