# Telekit framework
[![Latest Stable Version](http://poser.pugx.org/rasa/telekit/v)](https://packagist.org/packages/rasa/telekit) 
[![Total Downloads](http://poser.pugx.org/rasa/telekit/downloads)](https://packagist.org/packages/rasa/telekit) 
[![Latest Unstable Version](http://poser.pugx.org/rasa/telekit/v/unstable)](https://packagist.org/packages/rasa/telekit) 
[![License](http://poser.pugx.org/rasa/telekit/license)](https://packagist.org/packages/rasa/telekit) 
[![PHP Version Require](http://poser.pugx.org/rasa/telekit/require/php)](https://packagist.org/packages/rasa/telekit)

# About Telekit
Telekit is a powerful toolkit for building Telegram bots with ease, using Laravel Eloquent ORM. With Telekit, developers can quickly build and deploy bots, saving time and effort.

One of the standout features of Telekit is its support for Laravel Eloquent ORM, which allows developers to easily interact with the database and perform complex operations with just a few lines of code. This makes it simple to manage data within your Telegram bot and keep your code organized and maintainable.

Telekit also provides a range of tools and utilities for working with the Telegram API, making it easy to handle incoming messages, send messages to users, and perform other common bot tasks. Developers can take advantage of Telekit's intuitive API to quickly build and test new bot features, without having to worry about the underlying details of the Telegram protocol.



# Requirements
* PHP version 8.0
* Postgres, Mysql, Sqlite database
* Running migrations (chats table)

# To install Telekit library using Composer, you can follow these steps:

Open a terminal or command prompt and navigate to your project directory.

Run the following command:
```
composer require rasa/telekit
```
This will download and install the Telekit library and all its dependencies into your project's vendor directory.

Once the installation is complete, you can start using Telekit by including its autoload file in your code

 * **[my website](https://rasa.uz)**
 * **Receive updates in the browser** https://api.telegram.org/bot{TOKEN}/getUpdates
 * **Set up a webhook** https://api.telegram.org/bot{TOKEN}/setwebhook?url=https://test.com/example_bot/index.php
 * **Delete a webhook** https://api.telegram.org/bot{TOKEN}/deleteWebhook

# <h1 style="cyan:purple;">"artisan" commands</h1>
## With the command _"php artisan"_, you can interact with the bot. Here are some examples:
## Polling
### Process requests on your local computer without using hooks using the polling method
```
php artisan serve
```
# To see all available commands
```
php artisan
```
## Sending messages
### Sends to all users
```
php artisan send all
```
### Specify the chat id
```
php artisan send --to=CHAT_ID
php artisan send -t CHAT_ID
```
### Specify message
```
php artisan send --to=CHAT_ID --message="hello world"
php artisan send -t CHAT_ID -m "hello world"
```

## "make" command
### Creating template Interaction files
```
php artisan make:interaction MyInteraction
```
### Creating template Trigger files
```
php artisan make:trigger myTrigger 
```
### Creating template Migration files
```
php artisan make:migration table 
```
### Creating template Model files
```
php artisan make:model User 
```

## "database" command
### Get database
```
php artisan database
```
### Parameters
```
php artisan database:params
```
### Check database version
```
php artisan database:version
```
### Show tables
```
php artisan database:tables
```
### Output table contents
```
php artisan database:table chats
```
### Describe table
```
php artisan database:table chats --desc
```
### Output the contents of a specific table field
```
php artisan database:showTable chats --columns="chat_id"
php artisan database:showTable chats --columns="chat_id" --columns="username" --columns="attempts"
```
### run migrations
```
php artisan migrate
```
### undo all migration
```
php artisan migrate --fresh
```
## See responses
```
php artisan responses
```

# <h1 style="color:cyan;">Send responses</h1>
## reply message
```
class Hi extends Trigger {
    public function __construct($request)
    {
        $this->replyMessage('Hi');
    }
}
```
## Send message
```
<?php
namespace Triggers;

class Help extends Trigger {
    public function __construct($request)
    {
        $this->sendMessage()
            ->chatId($request['message']['chat']['id'])
            ->text('Info')
            ->send();
    }
}
```

### Send message with buttons
```
$this->sendMessage()
    ->chatId($request['message']['chat']['id'])
    ->text('Choose the option')
    ->parseMode()
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
    ->chatId(id)
    ->media([
            ['type' => 'document', 'name' => 'mycat', 'path' => 'img/image1.png'],
            ['type' => 'document', 'name' => 'mycat2', 'path' => 'img/image1.png']
    ])
    ->send();
```

# <h1 style="color:cyan;">InlineQueries</h1>
```
namespace Inlines;

use Core\Responses\Interaction;

class Example extends Interaction {
    public function __construct($request)
    {
        $result = [
            [
                "type" => "article",
                "id" => "0",
                "title" => "Do",
                "description" => "something",
                "input_message_content" => [
                    "message_text" => "result: <b> OK </b>",
                    "parse_mode" => "HTML"
                ]
            ],
            [
                "type" => "article",
                "id" => "1",
                "title" => "Do 2",
                "description" => "something 2",
                "input_message_content" => [
                    "message_text" => "result: <b> OK 2 </b>",
                    "parse_mode" => "HTML"
                ]
            ]
        ];

        $this->answerInlineQuery()
            ->inline_query_id($request['inline_query']['id'])
            ->results($result)
            ->cache_time(1)
            ->is_personal(true)
            ->send(false, false);
    }
}
```
## Send invoice
```
$this->sendInvoice()
    ->title('Subscription')
    ->description("Buy subscription for an month.")
    ->payload('Subscription: month')
    ->currency('USD')
    ->prices(['label' => 'Subscription for an month', 'amount' => 100000])
    ->send();
```
