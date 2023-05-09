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

    public static function isPreCheckout(): bool
    {
        return isset($GLOBALS['request']['pre_checkout_query']['invoice_payload']);
    }

    public static function isSuccessful(): bool
    {
        return isset($GLOBALS['request']['message']['successful_payment']);
    }
}