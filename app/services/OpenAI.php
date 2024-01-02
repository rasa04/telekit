<?php

namespace Services;

use Core\Env;
use OpenAI\Client;
use OpenAI\Responses\Audio\TranscriptionResponse;

final class OpenAI
{
    use Env;
    public static function client(): Client
    {
        return \OpenAI::client(apiKey: self::openAIKey());
    }

    public static function ask(array $messages): string
    {
        return self::client()->chat()->create([
            "model" => "gpt-3.5-turbo",
            "messages" => $messages,
        ])->choices[0]->message->content;
    }

    public static function transcribe(string $fileLink): TranscriptionResponse
    {
        return self::client()->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($fileLink, 'r'),
            'response_format' => 'verbose_json',
        ]);
    }

    public static function askCURL(array $messages): string
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::openAIKey(),
            ],
            'json' => [
                "model" => "gpt-3.5-turbo",
                "messages" => $messages,
            ],
            'verify' => false,
        ]);

        $result = json_decode($response->getBody()->getContents(), true)['choices'][0]['message']['content'];

        return (strlen($result) < 4096) ? $result : substr($result, 0, 4096);
    }

    public static function transcribeCURL(string $fileLink): string
    {
        $client = new \GuzzleHttp\Client();
        return json_decode(
            json: $client->post(
                uri: 'https://api.openai.com/v1/audio/transcriptions',
                options: [
                    'headers' => ['Authorization' => 'Bearer ' . self::openAIKey()],
                    'multipart' => [
                        [
                            'name'     => 'file',
                            'contents' => fopen($fileLink, 'r')
                        ],
                        [
                            'name' => 'model',
                            'contents' => 'whisper-1',
                        ]
                    ],
                    'verify' => false
                ]
            )->getBody()->getContents(),
            associative: 1
        )['text'];
    }
}