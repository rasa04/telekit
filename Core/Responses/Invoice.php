<?php

namespace Core\Responses;

use Core\Methods\answerPreCheckoutQuery;
use Core\Methods\SendMessage;

class Invoice
{
    public static function invoice(): array
    {
        return $GLOBALS['request']['pre_checkout_query'];
    }

    public static function answerPreCheckoutQuery($ok): void
    {
        new answerPreCheckoutQuery($ok);
    }

    public function reply_message(string $message): void
    {
        (new SendMessage)
            ->chat_id($GLOBALS['request']['pre_checkout_query']['from']['id'])
            ->text($message)
            ->send();
    }
}