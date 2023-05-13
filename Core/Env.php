<?php
namespace Core;

trait Env {
    public static function token(): array|false|string
    {
        return getenv("TELEGRAM_TOKEN");
    }
    public static function storage_path(): array|false|string
    {
        return __DIR__ . "/../storage/";
    }
    public static function path(): string
    {
        return __DIR__;
    }
    public static function app_path(): string
    {
        return __DIR__ . "/..";
    }
    public function gpt_token(): string
    {
        return getenv("GPT_TOKEN");
    }
    public function file_message(): string
    {
        return __DIR__ . "\..\storage\message.txt";
    }
    public function file_data(): string
    {
        return __DIR__ . "\..\storage\data.json";
    }
    public function default_chat_id(): int
    {
        return -872746594;
    }
    public function default_user_id(): int
    {
        return 511703056;
    }

    public static function time_zone(): string
    {
        return getenv("TIME_ZONE");
    }
    public function env(string $key, string $separator = null): array|false|string
    {
        return ($separator) ? explode($separator, getenv($key)) : getenv($key);
    }
}
