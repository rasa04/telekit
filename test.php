<?php
require "vendor/autoload.php";

use Core\Database\Database;
use Database\models\Country;
use Database\models\Group;
use Database\models\Chat;

new Database;

function list_countries(): void
{
    var_dump(Country::all(['code', 'name'])->toArray());
}

function list_context(): void
{
//    $result = Capsule::table('groups')
//        ->where('group_id', '=', -805540894)->value('context');
//    var_dump($result);

    $result = Group::where('group_id', '=', -805540894)->first('context');
    var_dump($result->toArray()["context"]);
}

function update_context(): void
{
    Chat::where('user_id', '=', 511703056)->update([
        'context' => '[{"name":"my","none":"yeah"}]'
    ]);
}

function openai(): void
{
    $request = [
        'message' => [
            'chat' => [
                'id' => 511703056
            ],
            'from' => [
                'id'=> 511703056
            ],
            'voice' => [
                "duration" => 2,
                "mime_type" => "audio/ogg",
                "file_id" => "AwACAgIAAxkBAAIQb2RP8C-adNzW5wWRlXf2CATmQznNAALqMwACf8aBSjybecAZOahcLwQ",
                "file_unique_id" => "AgAD6jMAAn_GgUo",
                "file_size" => 9071
            ]
        ]
    ];

    $GLOBALS['request'] = $request;
    new Responses\Triggers\OpenAI($request);
}

function test_triggers(): void
{
    $request = [
        'message' => [
            'chat' => [
                'id' => -805540894
            ],
            'from' => [
                'id'=> 511703056
            ],
            'text' => 'сколько будет 5+5?'
        ]
    ];
//    new \Responses\Start($request);
//    new \Responses\Help($request);
//    new \Responses\Settings($request);
}
function test_plots(): void
{
    $request = [
        'callback_query' => [
            'message' => [
                'chat' => [
                    'id' => 511703056
                ],
                'from' => [
                    'id'=> 511703056
                ],
                'text' => 'сколько будет 5+5?'
            ]
        ]
    ];

//    new Responses\Callbacks\About($request);
    new Responses\Callbacks\Settings($request);
}

//list_countries();
//list_context();
//update_context();
openai();
//test_triggers();
//test_plots();