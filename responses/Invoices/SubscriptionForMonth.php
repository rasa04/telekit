<?php

namespace Responses\Invoices;

use Core\Responses\Invoice;

class SubscriptionForMonth extends Invoice
{
    public function __construct()
    {
        if (self::isPreCheckout()) {
            self::answerPreCheckoutQuery(true);
            $this->reply_message("Проверка...");
        }
        elseif (self::isSuccessful()) {
            $this->reply_message("Оплата проведена успешно! Приятного пользования:)");
        }

        var_dump(['test' => $GLOBALS['request']]);
    }
}