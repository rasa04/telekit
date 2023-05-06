<?php

namespace Database\seeders;

use Database\models\Country;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //USERS
        Capsule::table('chats')->insert([
            'chat_id' => 511703056,
            'first_name' => '×_×',
            'username' => 'rasa035',
            'language' => 'en',
            'context' => '[]',
            'rights' => 2,
            'type' => 'private'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => 748856943,
            'context' => '[]',
            'rights' => 1,
            'type' => 'private'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => 250114420,
            'context' => '[]',
            'rights' => 1,
            'type' => 'private'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => 272004963,
            'context' => '[]',
            'rights' => 1,
            'type' => 'private'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => 679002894,
            'context' => '[]',
            'rights' => 1,
            'type' => 'private'
        ]);
        // GROUPS
        Capsule::table('chats')->insert([
            'chat_id' => -1001765736589,
            'context' => '[]',
            'rights' => 1,
            'type' => 'supergroup'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => -805540894,
            'context' => '[]',
            'rights' => 1,
            'type' => 'group'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => -1001673287453,
            'context' => '[]',
            'rights' => 1,
            'type' => 'supergroup'
        ]);
        Capsule::table('chats')->insert([
            'chat_id' => -824923223,
            'context' => '[]',
            'rights' => 1,
            'type' => 'group'
        ]);

        $countries = json_decode(file_get_contents(__DIR__ . '/../../storage/countries.json'), true);
        foreach ($countries as $countryCode => $countryName) {
            Country::insert([
                "code" => $countryCode,
                "name" => $countryName
            ]);
        }

    }
}