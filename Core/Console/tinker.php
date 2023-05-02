<?php
require "vendor/autoload.php";

use Core\Database\Database;
use Database\models\Country;
use Database\models\Group;
use Database\models\User;

new Database;
function seed_users(): void
{
    $users = [
        511703056,
        748856943,
        250114420,
        272004963,
        679002894
    ];
    foreach ($users as $user) {
        $result = User::insert([
            "user_id" => $user,
            "role" => 'pro',
            "context" => json_encode([]),
        ]);
    }
    var_dump($result);
}
function seed_groups(): void
{
    $groups = [
        -1001765736589,
        -805540894,
        -1001673287453,
        -824923223,
    ];
    foreach ($groups as $group) {
        $result = Group::insert([
            "group_id" => $group,
            "rights" => "pro",
            "context" => json_encode([]),
        ]);
    }
    var_dump($result);
}

function seed_countries(): void
{
    $countries = json_decode(file_get_contents(__DIR__ . '/../../storage/countries.json'), true);
    $result = [];
    foreach ($countries as $countryCode => $countryName) {
        $result[] = Country::insert([
            "code" => $countryCode,
            "name" => $countryName
        ]);
    }
    var_dump($result);
}

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
    User::where('user_id', '=', 511703056)->update([
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
//    new \Triggers\Start($request);
//    new \Triggers\Help($request);
//    new \Triggers\Settings($request);
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

//    new Responses\Plots\About($request);
    new Responses\Plots\Settings($request);
}

//SEEDERS
//seed_users();
//seed_groups();
//seed_countries();
//
//list_countries();
//list_context();
//update_context();
openai();
//test_triggers();
//test_plots();