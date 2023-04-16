<?php
namespace Core;

trait Env {
    public function token(): array|false|string
    {
        return getenv("TELEGRAM_TOKEN");
    }
    public function storage(): array|false|string
    {
        return __DIR__ . "/../storage/";
    }
    public function path(): string
    {
        return __DIR__;
    }
    public function app_path(): string
    {
        return __DIR__ . "/..";
    }
    public function gpt(): string
    {
        return getenv("GPT_TOKEN");
    }
    public function file_message(): string
    {
        return __DIR__ . "/../storage/message.txt";
    }
    public function file_data(): string
    {
        return __DIR__ . "/../storage/data.json";
    }
    public function default_chat_id(): int
    {
        return -872746594;
    }
    public function default_user_id(): int
    {
        return 511703056;
    }
    public function pro_users(): array|false|string
    {
        return explode(",", getenv("pro_users"));
    }
    public function env(string $key, string $separator = null): array|false|string
    {
        return ($separator) ? explode($separator, getenv($key)) : getenv($key);
    }
}
