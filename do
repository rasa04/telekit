<?php
require_once('./vendor/autoload.php');

use Core\Methods\SendMessage;
use Dotenv\Dotenv;
Dotenv::createUnsafeImmutable(__DIR__)->load();

$short_options = "";
$long_options = [
    "send",
    "to::",
    "message::",
    "trigger::",
    "interaction::",
];

$command_name = $argv[0];
$options = getopt(short_options: $short_options, long_options: $long_options);

// CHECK
if ($argv[1] === "--send") {
    $chat_id = $options["to"] ?? getenv("DEFAULT_USER");
    $message = $options["message"] ?? "Hi! It's test message from: " . getenv("APP_NAME");
    (new SendMessage)
        ->chat_id($chat_id)
        ->text($message)
        ->send();
    response($options);
}

// MAKE
if ($argv[1] === "--make") {
    $samples = __DIR__ . "\Core\Console\Kernel\Samples\\";
    $responses = __DIR__ ."\Responses\\";

    if (isset($options["interaction"])) {
        $file = $samples . "Interaction.php";
        $new_file = $responses . "\Interactions\\" . $options["interaction"] . ".php";
        response(["file" => $new_file], copy($file, $new_file));
    }
    elseif (isset($options["trigger"])) {
        $file = $samples . "Trigger.php";
        $new_file = $responses . "\Triggers\\" . $options["trigger"] . ".php";
        response(["file" => $new_file], copy($file, $new_file));
    }
    else {
        response("NO COMMANDS", false);
    }
}

// RESPONSE TEMPLATE
function response($show, $check = true): void
{
    var_dump($show);
    echo ($check) ? "--#SUCCESS#--" : "--#ERROR#--";
}

