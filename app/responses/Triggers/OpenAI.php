<?php
namespace Responses\Triggers;

use Core\API\Types\Message;
use Core\Interface\Trigger as TriggerInterface;
use Core\Responses\Trigger;
use Database\models\Chat;
use Exception;

class OpenAI extends Trigger implements TriggerInterface {
    private const MAX_ATTEMPTS = 150;
    private array $messages = [];
    private array $errorMessages = [
        'LIMIT_REACHED' => 'Вы достигли лимита сообщений. Подробнее: /subscription',
    ];

    public function __construct(array $request, ?Message $message)
    {
        /** @var Chat $chat */
        $chat = Chat::query()->firstWhere(column: 'chat_id', operator: $request['message']['chat']['id']);
        $attempts = $chat->getAttribute('attempts');
        $context = $chat->getAttribute('context');

        if ($message->chat()->isPrivate() && $attempts >= self::MAX_ATTEMPTS) {
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
                    $this->log('TELEKIT ERROR: zero again');
                }
            }

            if (isset($this->requestMessage()['voice']) && $message->chat()->isPrivate()) {
                // GET FILE LINK
                $file_path = "https://api.telegram.org/bot" . $this->token() . "/getFile?file_id=" . $request['message']['voice']['file_id'];
                $response = json_decode(file_get_contents($file_path), true);
                $file_path = $response['result']['file_path'];
                $download_url = "https://api.telegram.org/file/bot" . $this->token() . "/" . $file_path;
                // CHANGE FORMAT FROM OGG TO MP3
                $fileLink = $this->storage_path() . "voices/vm_" . rand() . '.mp3';
                exec("ffmpeg -i $download_url -vn -ar 44100 -ac 2 -ab 192k -f mp3 $fileLink");

                $this->messages[] = [
                    "role" => "user",
                    "content" => \Services\OpenAI::transcribeCURL($fileLink)
                ];

                unlink($fileLink);
            } elseif (isset($this->requestMessage()['text'])) {
                $this->messages[] = [
                    "role" => "user",
                    "content" => $request['message']['text']
                ];
            } else {
                return;
            }

            $answer = \Services\OpenAI::askCURL($this->messages);

            $this->messages[] = [
                "role" => "assistant",
                "content" => $answer
            ];

            $this->messages = array_slice($this->messages, -10, 10);

            if ($context) {
                $chat->setAttribute('context', $this->messages);
                $chat->save();
            }

            $this->deleteMessage();
            $this->replyMessage($answer);
        }
    }
}
