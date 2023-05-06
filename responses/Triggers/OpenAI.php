<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;
use Database\models\Chat;

class OpenAI extends Trigger {
    private array $messages = [];

    public function __construct($request)
    {
        if (!$this->authorized() && $this->chat_is_private())
        {
            $this->send_invoice()
                ->title('Купить подписку на месяц')
                ->description("Купив подписку вы получите доступ ко всем функциям.")
                ->payload('Подписка на месяц')
                ->currency('UZS')
                ->prices(['label' => 'Подписка на месяц', 'amount' => 100000])
                ->send();
        }
        elseif ($this->authorized())
        {
            $chat = Chat::where('chat_id', $request['message']['chat']['id']);

            if ($chat->first('context')) {
                $this->messages = json_decode($chat->first('context')->toArray()["context"], true);
            }

            if (isset($this->request_message()['voice']) && $this->chat_is_private()) {
                // GET FILE LINK
                $file_path = "https://api.telegram.org/bot" . $this->token() . "/getFile?file_id=" . $request['message']['voice']['file_id'];
                $response = json_decode(file_get_contents($file_path), true);
                $file_path = $response['result']['file_path'];
                $download_url = "https://api.telegram.org/file/bot" . $this->token() . "/" . $file_path;
                // CHANGE FORMAT FROM OGG TO MP3
                $fileLink = $this->storage_path() . "voices/vm_" . rand() . '.mp3';
                exec("ffmpeg -i $download_url -vn -ar 44100 -ac 2 -ab 192k -f mp3 $fileLink");
                // TRANSCRIPT
                $response = $this->client()->post('https://api.openai.com/v1/audio/transcriptions', [
                    'headers' => ['Authorization' => 'Bearer ' . $this->gpt_token()],
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
                ]);

                $text = json_decode($response->getBody()->getContents(), true)['text'];

                $this->messages[] = [
                    "role" => "user",
                    "content" => $text
                ];

                unlink($fileLink);
            }
            elseif (isset($this->request_message()['text'])) {
                $this->messages[] = [
                    "role" => "user",
                    "content" => $request['message']['text']
                ];
            }
            else {
                return;
            }

            $answer = $this->chat_gpt($this->messages);

            $this->messages[] = [
                "role" => "assistant",
                "content" => $answer
            ];

            $this->messages = array_slice($this->messages, -10, 10);

            if ($chat->first('context')) $chat->first()->update(['context' => json_encode($this->messages)]);

            $this->reply_message($answer);
        }

    }
}