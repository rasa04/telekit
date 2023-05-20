<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;
use Database\models\Chat;
use Exception;

class OpenAI extends Trigger {
    private array $messages = [];

    public function __construct($request)
    {
        $chat = Chat::where('chat_id', $request['message']['chat']['id']);
        $attempts = $chat->first('attempts')->toArray()['attempts'];
        $MAX_ATTEMPTS = 15;

        if ($this->chat_is_private() && $attempts >= $MAX_ATTEMPTS)
        {
            $this->reply_message("Вы достигли лимита сообщений. Подробнее: /subscription");
        }
        elseif ($this->authorized() || $attempts < $MAX_ATTEMPTS)
        {
            $this->reply_message('🤔');
            if (!$this->authorized()) {
                $temp_chat = $chat->first();
                $temp_chat->attempts = $attempts+1;
                $temp_chat->save();
            }
            if ($chat->first('context')) {
                try {
                    $this->messages = json_decode($chat->first('context')->toArray()["context"], true);
                } catch (Exception) {
                    $this->writeLogFile('TELEKIT ERROR: zero again');
                }
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

            $this->deleteMessage();
            $this->reply_message($answer);
        }

    }
}