<?php

namespace Responses\Invoices;

use Core\Responses\Invoice;

class SubscriptionForMonth extends Invoice
{
    public function __construct()
    {
        if (self::isPreCheckout()) {
            self::answerPreCheckoutQuery(true);
            $this->replyMessage("Проверка...");
        }
        elseif (self::isSuccessful()) {
            $this->replyMessage("Оплата проведена успешно! Приятного пользования:)");
        }

        var_dump(['test' => $GLOBALS['request']]);
    }
}