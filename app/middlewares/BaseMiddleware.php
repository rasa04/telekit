<?php
namespace Middlewares;

use Core\API\Types\CallbackQuery;
use Database\models\Chat;
use Core\API\Types\Message;
use Core\Interface\Middleware;

class BaseMiddleware implements Middleware
{
    public function handle(array $request, ?Message $message, ?CallbackQuery $callbackQuery): void
    {
        if (!isset($request['message']) || !is_array($request['message'])) {
            return;
        }
        $message = new Message($request['message']);
        $chat = Chat::query()->firstWhere('chat_id', $message->chat()->id());

        if ($chat) {
            if ($message->chat()->isPrivate()) {
                $data = [
                    'first_name' => $message->from()->firstName(),
                    'username' => $message->from()->username(),
                    'language' => $message->from()->languageCode(),
                ];
            } elseif ($message->chat()->isGroup()) {
                $data['first_name'] = $message->chat()->title();
            } elseif ($message->chat()->isSupergroup()) {
                $data['first_name'] = $message->chat()->title();
                $data['username'] = $message->chat()->username();
            }
            $chat->update($data ?? []);
        } else {
            $data = [
                "chat_id" => $message->chat()->id(),
                "rights" => 0,
                "context" => '[]',
                "type" => $message->chat()->type(),
            ];
            if ($message->chat()->isPrivate()) {
                $data['first_name'] = $request['message']['from']['first_name'];
                $data['username'] = $request['message']['from']['username'];
                $data["language"] = $request['message']['from']['language_code'];
            } elseif ($message->chat()->isGroup()) {
                $data['first_name'] = $request['message']['chat']['title'];
            } elseif ($message->chat()->isSupergroup()) {
                $data['first_name'] = $request['message']['chat']['title'];
                $data['username'] = $request['message']['chat']['username'];
            }
            Chat::query()->insert($data);
        }
    }
}