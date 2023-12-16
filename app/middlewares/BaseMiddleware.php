<?php
namespace Middlewares;

use Database\models\Chat;
use Core\Entities\Message;

class BaseMiddleware implements Middleware
{
    public function handle(array $request, Message $message): void
    {
        if (!isset($request['message']) && !is_array($request['message'])) {
            return;
        }
        $message = new Message($request['message']);

        $chat = Chat::query()->firstWhere('chat_id', $message->getChatID());

        if ($chat) {
            if ($message->isPrivate()) {
                $data = [
                    'first_name' => $message->getUserFirstName(),
                    'username' => $message->getUsername(),
                    'language' => $message->getLangCode(),
                ];
            } elseif ($message->isGroup()) {
                $data['first_name'] = $message->getChatTitle();
            } elseif ($message->isSupergroup()) {
                $data['first_name'] = $message->getChatTitle();
                $data['username'] = $message->getChatUserName();
            }
            $chat->update($data ?? []);
        } else {
            $data = [
                "chat_id" => $message->getChatID(),
                "rights" => 0,
                "context" => '[]',
                "type" => $message->getChatType(),
            ];
            if ($message->isPrivate()) {
                $data['first_name'] = $request['message']['from']['first_name'];
                $data['username'] = $request['message']['from']['username'];
                $data["language"] = $request['message']['from']['language_code'];
            } elseif ($message->isGroup()) {
                $data['first_name'] = $request['message']['chat']['title'];
            } elseif ($message->isSupergroup()) {
                $data['first_name'] = $request['message']['chat']['title'];
                $data['username'] = $request['message']['chat']['username'];
            }
            Chat::insert($data);
        }
    }
}