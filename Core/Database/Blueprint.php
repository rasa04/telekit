<?php

namespace Core\Database;

class Blueprint
{
    public array $columns = [
        "id" => false,
        "integer" => [],
        "string" => [],
        "json" => [],
    ];

    public function __construct()
    {
        return $this->columns;
    }

    public function id(): void
    {
        $this->columns["id"] = true;
    }

    public function integer(string $column): void
    {
        $this->columns["integer"][] = $column;
    }

    public function string(string $column): void
    {
        $this->columns["string"][] = $column;
    }

    public function json(string $column): void
    {
        $this->columns["json"][] = $column;
    }
}