# TELEKIT FRAMEWORK

# GET STARTED
 * :bulb: **Find contacts on [my website](https://matesite.uz)**
 * **Receive updates in the browser** https://api.telegram.org/bot{TOKEN}/getUpdates
 * **Set up a webhook** https://api.telegram.org/bot{TOKEN}/setwebhook?url=https://test.com/example_bot/index.php
 * **Delete a webhook** https://api.telegram.org/bot{TOKEN}/deleteWebhook

# Requirements
* PHP version 8.0

# To install Telekit library using Composer, you can follow these steps:

Open a terminal or command prompt and navigate to your project directory.

Run the following command:
```
composer require rasa/telekit
```
This will download and install the Telekit library and all its dependencies into your project's vendor directory.

Once the installation is complete, you can start using Telekit by including its autoload file in your code

# <h1 style="cyan:purple;">"do" commands</h1>
## With the command _"php do"_, you can interact with the bot. Here are some examples:

## Sending messages
```
php do --send (sends default message to default user from .env file)
php do --send --to=CHAT_ID
php do --send --to=CHAT_ID --message="hello world"
```

## "make" command
### Creating template Interaction files
```
php do --make --interaction=MyInteraction
```
### Creating template Trigger files
```
php do --make --trigger=myTrigger 
```
### Creating template Migration files
```
php do --make --migration=table 
```

## "database" command
### Get database
```
php do --database
```
### Check database connection
```
php do --database --connection
```
### Check database version
```
php do --database --version
```
### Show tables
```
php do --database --tables
```
### Describe table
```
php do --database --table=users
```
### Output table contents
```
php do --database --showTable=users
```
### Output the contents of a specific table field
```
php do --database --showTable=users --column="user_id"
```
### run migrations
```
php do --database --migrate
```
### undo all migration
```
php do --database --migrate --fresh
```

# <h1 style="color:cyan;">Send responses</h1>
## Send message
```
<?php
namespace Triggers;

class Help extends Trigger {
    public function __construct($request)
    {
        $this->message()
            ->chat_id($request['message']['chat']['id'])
            ->text('Помощь')
            ->send();
    }
}
```

### Send message with buttons
```
$this->message()
    ->chat_id($request['message']['chat']['id'])
    ->text('Choose the option')
    ->parse_mode()
    ->reply_markup([
        'one_time_keyboard' => true,
        'resize_keyboard' => true,
        'inline_keyboard' => [
            [
                [
                    'text' => 'About',
                    'callback_data' => 'About',
                ],
                [
                    'text' => 'Help',
                    'callback_data' => 'help',
                ]
            ],
            [
                [
                    'text' => 'Settings',
                    'callback_data' => 'settings',
                ]
            ]
        ]
    ])
    ->send();
```

## Send photo
```
$this->photo()
    ->protect_content(true)
    ->caption('Подпись')
    ->photo("image1.png", "кот.jpg", "image/jpg")
    ->send()
```

## Send document
```
$this->document()
    ->protect_content(true)
    ->caption('Sign')
    ->photo("image1.png", "cat.jpg", "image/jpg")
    ->send()
```

## Send media files
```
$this->mediaGroup()
    ->chat_id(id)
    ->media([
            ['type' => 'document', 'name' => 'mycat', 'path' => 'img/image1.png'],
            ['type' => 'document', 'name' => 'mycat2', 'path' => 'img/image1.png']
    ])
    ->send();
```

# <h1 style="color:cyan;">InlineQueries</h1>
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