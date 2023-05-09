<?php
namespace Responses\Triggers;

use Core\Responses\Trigger;

class Subscription extends Trigger {
    public function __construct()
    {
        $this->reply_message(
        "Если Вам функционал бота показался полезным, поддержите меня подпиской."
        ."\n\nЭто поможет мне поддерживать стабильную и быструю работу сервера, каждая подписка способствует появлению новых фич и удешевлению существующего функционала."
        ."\n\nПодписка стоит 2$ или 100руб или 15000 сум в месяц. Обратитесь к @rasa035"
        );
    }
}