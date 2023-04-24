<?php

namespace Core\Storage;

use Core\Env;

class Storage
{
    use Env;

    static string $path = __DIR__ . "/../../storage/";

    static function get(string $file, $associative = null): array
    {
        return json_decode(file_get_contents(((new Storage)->storage_path() ?? self::$path) . $file), $associative) ?? [];
    }

    static function save(array $data, string $file = "data.json", bool $overwrite = false): void
    {
        $file_link = ((new Storage)->storage_path() ?? self::$path) . $file;
        $file_content = json_decode(file_get_contents($file_link)) ?? [];
        (!$overwrite) ? $file_content[] = $data : $file_content = $data;
        file_put_contents($file_link, json_encode($file_content));
    }
}