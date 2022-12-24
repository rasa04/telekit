<?php
namespace Core;

Class Methods{
    public static $getMe = 'getMe';
    public static $logOut = 'logOut';
    public static $close = 'close';
    public static $forwardMessage = 'forwardMessage';
    public static $copyMessage = 'copyMessage';
    public static $sendAudio = 'sendAudio';
    public static $sendVideo = 'sendVideo';
    public static $sendAnimation = 'sendAnimation';
    public static $sendVoice = 'sendVoice';
    public static $sendVideoNote = 'sendVideoNote';
    public static $sendLocation = 'sendLocation';
    public static $editMessageLiveLocation = 'editMessageLiveLocation';
    public static $stopMessageLiveLocation = 'stopMessageLiveLocation';
    public static $sendVenue = 'sendVenue';
    public static $sendContact = 'sendContact';
    public static $sendPoll = 'sendPoll';
    public static $sendDice = 'sendDice';
    public static $sendChatAction = 'sendChatAction';
    public static $getUserProfilePhotos = 'getUserProfilePhotos';
    public static $getFile = 'getFile';
    public static $unbanChatMember = 'unbanChatMember';
    public static $restrictChatMember = 'restrictChatMember';
    public static $promoteChatMember = 'promoteChatMember';
    public static $setChatAdministratorCustomTitle = 'setChatAdministratorCustomTitle';
    public static $banChatSenderChat = 'banChatSenderChat';
    public static $unbanChatSenderChat = 'unbanChatSenderChat';
    public static $sendMessage = 'sendMessage';
    public static $setChatPermissions = 'setChatPermissions';
    public static $exportChatInviteLink = 'exportChatInviteLink';
    public static $createChatInviteLink = 'createChatInviteLink';
    public static $editChatInviteLink = 'editChatInviteLink';
    public static $revokeChatInviteLink = 'revokeChatInviteLink';
    public static $approveChatJoinRequest = 'approveChatJoinRequest';
    public static $declineChatJoinRequest = 'declineChatJoinRequest';
    public static $setChatPhoto = 'setChatPhoto';
    public static $deleteChatPhoto = 'deleteChatPhoto';
    public static $setChatTitle = 'setChatTitle';
    public static $setChatDescription = 'setChatDescription';
    public static $pinChatMessage = 'pinChatMessage';
    public static $unpinChatMessage = 'unpinChatMessage';
    public static $unpinAllChatMessages = 'unpinAllChatMessages';
    public static $leaveChat = 'leaveChat';
    public static $getChat = 'getChat';
    public static $getChatAdministrators = 'getChatAdministrators';
    public static $getChatMemberCount = 'getChatMemberCount';
    public static $getChatMember = 'getChatMember';
    public static $setChatStickerSet = 'setChatStickerSet';
    public static $deleteChatStickerSet = 'deleteChatStickerSet';
    public static $getForumTopicIconStickers = 'getForumTopicIconStickers';
    public static $createForumTopic = 'createForumTopic';
    public static $editForumTopic = 'editForumTopic';
    public static $closeForumTopic = 'closeForumTopic';
    public static $reopenForumTopic = 'reopenForumTopic';
    public static $deleteForumTopic = 'deleteForumTopic';
    public static $unpinAllForumTopicMessages = 'unpinAllForumTopicMessages';
    public static $answerCallbackQuery = 'answerCallbackQuery';
    public static $setMyCommands = 'setMyCommands';
    public static $deleteMyCommands = 'deleteMyCommands';
    public static $getMyCommands = 'getMyCommands';
    public static $setChatMenuButton = 'setChatMenuButton';
    public static $getChatMenuButton = 'getChatMenuButton';
    public static $setMyDefaultAdministratorRights = 'setMyDefaultAdministratorRights';
    public static $getMyDefaultAdministratorRights = 'getMyDefaultAdministratorRights';
    public static $sendPhoto = 'sendPhoto';
    public static $sendDocument = 'sendDocument';
    public static $sendMediaGroup = 'sendMediaGroup';


    public static function writeLogFile($str, $clear = false){
        $log_file_name = __DIR__ . "/../storage/" . "message.txt";
        $now = date("Y-m-d H:i:s");
        if ($clear == false) {
            file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
        }else{
            file_put_contents($log_file_name, '');
            file_put_contents($log_file_name, $now . " " . print_r($str, true) .  "\r\n", FILE_APPEND);
        }
    }

    public static function requestListener($Consts){ 
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);

        if (!empty($data["message"]["photo"])) {
            $file_id = $data["message"]["photo"][3]["file_id"];
            $file = [
                "file_id" => $file_id,
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/getFile?" . http_build_query($file),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_SSL_VERIFYPEER => 0,
                // CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers),
            ]);
            
            $result = curl_exec($curl);
            curl_close($curl);

            // записываем ответ в формате PHP массива
            $dataResult = json_decode($result, true);
            // записываем URL необходимого изображения
            $fileUrl = $dataResult["result"]["file_path"];
            // формируем полный URL до файла
            $photoPathTG = "https://api.telegram.org/file/bot" . $Consts::TOKEN . "/" . $fileUrl;

            self::writeLogFile($photoPathTG, true);
            // забираем название файла
            $newFilePath = __DIR__ . "/../storage/img/" . explode("/", $fileUrl)[1];

            // сохраняем файл на серсер
            file_put_contents($newFilePath, file_get_contents($photoPathTG));
        }

    }

    public static function sendTelegram($Consts, $method, $data, $headers = [])
    {
        $curl = curl_init();
            if($method == 'sendMessage'){
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/$method?" . http_build_query($data::$data),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => json_encode($data::$data),
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers),
            ]);
        }
        else if($method == 'sendDocument'){
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/$method?" . http_build_query($data::$data),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => json_encode($data::$data),
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);
        }
        else if($method == 'sendPhoto'){
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/$method?" . http_build_query($data::$data),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => json_encode($data::$data),
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);
        }
        else if($method == 'sendMediaGroup'){
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.telegram.org/bot' . $Consts::TOKEN . "/$method?" . http_build_query($data::$data),
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POSTFIELDS => $data::$data,
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);
        }
        
        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, 1) ? json_decode($result, 1) : $result); // параметр 1 или true преобразует в ассоциативный массив
    }

    public static function sendTelegramWithoutCurl($Consts, $method, $textMessage){
        // обычный запрос, без curl
        $urlQuery = 'https://api.telegram.org/bot' . $Consts::TOKEN . '/' . $method . "?chat_id=" . $Consts::TG_USER_ID . "&text=$textMessage";
        $result = file_get_contents($urlQuery);
    }

}
