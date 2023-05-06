<?php

namespace Responses\Invoices;

use Core\Responses\Invoice;

class SubscriptionForMonth extends Invoice
{
    public function __construct()
    {
        self::answerPreCheckoutQuery(true);
        $this->reply_message("Оплата успешно прошла!");
    }
}