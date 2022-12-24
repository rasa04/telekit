<?php
require_once('./vendor/autoload.php');

$Consts = new Core\Consts();
$method = Core\Methods::$sendDocument;

/**
 * INFO
 * You can get updates with
 * https://api.telegram.org/bot5977797894:AAHIXhQ6uY5Lu9BxhLY_j0bZSmMK8nQxbSc/getUpdates
 * 
 * If you want to set webhooks to your server use
 * https://api.telegram.org/bot5977797894:AAHIXhQ6uY5Lu9BxhLY_j0bZSmMK8nQxbSc/setwebhook?url=https://matesite.uz/bots/birthdayReminder/index.php
 * 
 */

/* ***************   REQUEST LISTENER   ***************** */

Core\Methods::requestListener($Consts);

/* ***************   МЕТОДЫ ОТПРАВКИ   ***************** */

$textMessage = "Новое тестовое сообщение <b> формы </b>";

$data = Core\Methods::$method($Consts);

/* ***************   ФУНКЦИЯ ОТПРАВКИ   ***************** */

var_dump(Core\Methods::sendTelegram($Consts, $method, $data));
?>