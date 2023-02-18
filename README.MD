
# СПРАВОЧНИК
 * :bulb: **Контакты найдете на [моем сайте](matesite.uz)**
 * **Получать обновления в браузере**
 * https://api.telegram.org/bot{TOKEN}/getUpdates
 * **Настроить хук**
 * https://api.telegram.org/bot{TOKEN}/setwebhook?url=https://test.com/example_bot/index.php


## Пример настройки фото
```
$response = new Core\Methods\SendPhoto;
$response->protect_content(true)
        ->caption('Подпись')
        ->photo("image1.png", "кот.jpg", "image/jpg")
        ->send()
```

## Пример настройки документа
```
$response = new Core\Methods\SendPhoto;
$response->protect_content(true)
        ->caption('Подпись')
        ->photo("image1.png", "кот.jpg", "image/jpg")
        ->send()
```

## Пример настройки сообщения
```
namespace Triggers;

use Core\Methods\SendMessage;

class Help {
    public function __construct($request)
    {
        $response = new SendMessage;
        $response
            ->chat_id($request['message']['chat']['id'])
            ->text('Помощь')
            ->send();
    }
}
```

## Пример настройки сообщения с кнопками
```
$response = new SendMessage;

$response
    ->chat_id($request['message']['chat']['id'])
    ->text('Выберите опцию')
    ->parse_mode()
    ->reply_markup([
        'one_time_keyboard' => true,
        'resize_keyboard' => true,
        'inline_keyboard' => [
            [
                [
                    'text' => 'Назначить день рождение',
                    'callback_data' => 'Назначить день рождение',
                ],
                [
                    'text' => 'Назначить особый день',
                    'callback_data' => 'Назначить особый день',
                ]
            ],
            [
                [
                    'text' => 'События',
                    'callback_data' => 'События',
                ],
                [
                    'text' => 'Функции',
                    'callback_data' => 'Функции',
                ],
                [
                    'text' => 'Поддержка',
                    'callback_data' => 'Поддержка',
                ]
            ]
        ]
    ])
    ->send();
```

## Пример настройки отправки медиа файлов
```
$response = new \Core\Methods\SendMediaGroup;
$response->chat_id(id)
        ->media([
                ['type' => 'document', 'name' => 'mycat', 'path' => 'img/image1.png'],
                ['type' => 'document', 'name' => 'mycat2', 'path' => 'img/image1.png']
        ])
        ->send();
```

## Пример InlineQueries
```
namespace Interactions;

use Core\Methods\AnswerInlineQuery;

class Example {
    public function __construct($request)
    {
        $result = [
            [
                "type" => "article",
                "id" => "0",
                "title" => "Do",
                "description" => "something",
                "input_message_content" => [
                    "message_text" => "результат: <b> OK </b>",
                    "parse_mode" => "HTML"
                ]
            ],
            [
                "type" => "article",
                "id" => "1",
                "title" => "Do 2",
                "description" => "something 2",
                "input_message_content" => [
                    "message_text" => "результат: <b> OK 2 </b>",
                    "parse_mode" => "HTML"
                ]
            ]
        ];

        $response = new AnswerInlineQuery;
        $response->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->send();
    }
}
```