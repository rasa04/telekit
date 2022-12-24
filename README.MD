## Пример настройки фото
$Consts = new Core\Consts();
$method = Core\Methods::$sendPhoto;

$data = new Core\Methods\SendPhoto(['chat_id' => 511703056, 'caption' => "Новая подпись"]);
$data::protect_content(1);
$data::caption("Подпись");
$data::photo("image1.png", "кот.jpg", "image/jpg");


## Пример настройки документа
$Consts = new Core\Consts();
$method = Core\Methods::$sendDocument;

$data = new Core\Methods\SendDocument(['chat_id' => 511703056, 'caption' => "Новая подпись"]);
$data::protect_content(1);
$data::caption("Подпись");
$data::document("image1.png", "кот.jpg", "image/jpg");


## Пример настройки сообщения
$Consts = new Core\Consts();
$method = Core\Methods::$sendMessage;

$data = new Core\Methods\SendMessage([
    'chat_id' => 511703056,
    'text' => 'Привет, зай))', 
    'parse_mode' => 'html', 
    'reply_to_message_id' => null,
]);


## Пример настройки сообщения с кнопками
$Consts = new Core\Consts();
$method = Core\Methods::$sendMessage;

$data = new Core\Methods\SendMessage([
    'chat_id' => 511703056,
    'text' => 'Привет, зай))', 
    'parse_mode' => 'html', 
    'reply_to_message_id' => null,
    'reply_markup' => [
        'one_time_keyboard' => true,
        'resize_keyboard' => true,
        'keyboard' => [
            [
                ['text' => 'Назначить день рождение'],
                ['text' => 'Назначить особый день'],
            ],
            [
                ['text' => 'Функции'],
                ['text' => 'Поддержка'],
            ]
        ],
        'inline_keyboard' => [
            [
                [
                    'text' => 'Назначить день рождение',
                    'callback_data' => 'test_1',
                ],
                [
                    'text' => 'Функции',
                    'callback_data' => 'test_2',
                ]
            ],
            [
                [
                    'text' => 'Назначить день рождение',
                    'callback_data' => 'test_1',
                ],
                [
                    'text' => 'Функции',
                    'callback_data' => 'test_2',
                ],
                [
                    'text' => 'Функции 2',
                    'callback_data' => 'test_3',
                ]
            ]
        ]
    ]
]);


## Пример настройки отправки медиа файлов
$Consts = new Core\Consts();
$method = Core\Methods::$sendMediaGroup;

$data = new \Core\Methods\SendMediaGroup([
    "media" => [
        ['type' => 'document', 'name' => 'mycat', 'path' => 'image1.png'],
        ['type' => 'document', 'name' => 'mycat2', 'path' => 'image1.png']
    ]
]);