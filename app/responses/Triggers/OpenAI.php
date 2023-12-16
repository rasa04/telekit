<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;
use Database\models\Chat;
use Exception;

class OpenAI extends Trigger {
    private const MAX_ATTEMPTS = 15;
    private array $messages = [];
    private array $errorMessages = [
        'LIMIT_REACHED' => 'Вы достигли лимита сообщений. Подробнее: /subscription',
    ];

    public function __construct($request)
    {
        /** @var Chat $chat */
        $chat = Chat::query()->where(column: 'chat_id', operator: $request['message']['chat']['id'])->first();
        $attempts = $chat->getAttribute('attempts');
        $context = $chat->getAttribute('context');

        if ($this->chat_is_private() && $attempts >= self::MAX_ATTEMPTS) {
            $this->replyMessage($this->errorMessages['LIMIT_REACHED']);
            return;
        }

        if ($this->isAuthorized() || $attempts < self::MAX_ATTEMPTS) {
            if (!$this->isAuthorized()) {
                $chat->increaseTheNumberOfAttempts();
            }
            $this->replyMessage('🤔');
            if ($context) {
                try {
                    $this->messages = json_decode($context, true);
                } catch (Exception) {
                    $this->writeLogFile('TELEKIT ERROR: zero again');
                }
            }

            if (isset($this->requestMessage()['voice']) && $this->chat_is_private()) {
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
            elseif (isset($this->requestMessage()['text'])) {
                $this->messages[] = [
                    "role" => "user",
                    "content" => $request['message']['text']
                ];
            }
            else {
                return;
            }

            $answer = $this->chatGPT($this->messages);

            $this->messages[] = [
                "role" => "assistant",
                "content" => $answer
            ];

            $this->messages = array_slice($this->messages, -10, 10);

            if ($context) $chat->update(['context' => $this->messages]);

            $this->deleteMessage();
            $this->replyMessage($answer);
        }
    }
}
