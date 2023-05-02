<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;
use Database\models\Group;
use Database\models\User;

class OpenAI extends Trigger {
    private array $messages = [];

    public function __construct($request)
    {
        if (!$this->authorized())
        {
            $this->reply_message('Вы не авторизованы. Обратитесь к @rasa035');
        }
        else
        {
            $group = Group::where('group_id', $request['message']['chat']['id']);
            $user = User::where('user_id', $request['message']['chat']['id']);

            if ($group->first('context')) {
                $this->messages = json_decode($group->first('context')->toArray()["context"], true);
            }
            elseif ($user->first('context')) {
                $this->messages = json_decode($user->first('context')->toArray()["context"], true);
            }

            if (isset($this->request_message()['voice'])) {
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

            $answer = $this->chat_gpt($this->messages);

            $this->messages[] = [
                "role" => "assistant",
                "content" => $answer
            ];

            $this->messages = array_slice($this->messages, -10, 10);

            if ($group->first('context')) $group->first()->update(['context' => json_encode($this->messages)]);
            elseif ($user->first('context')) $user->first()->update(['context' => json_encode($this->messages)]);

            $this->reply_message($answer);
        }

    }
}