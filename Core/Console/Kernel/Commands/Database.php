<?php

namespace Core\Console\Kernel\Commands;

use Core\Env;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Tools\DsnParser;

class Database
{
    use Env;

    /**
     * @throws Exception
     */
    public function __construct($options, $argv)
    {
        $connectionParams = [
            'dbname' => $this->env("DATABASE"),
            'user' => $this->env("DB_USER"),
            'password' => $this->env("DB_PASSWORD"),
            'host' => $this->env("DB_HOST"),
            'driver' => $this->env("DB_DRIVER"),
        ];
        $database = DriverManager::getConnection($connectionParams);

        if ($argv[2] === "--connect") var_dump($database->getConfiguration());
        elseif ($argv[2] === "--get-databases") var_dump($database->createSchemaManager()->listDatabases());
        elseif ($argv[2] === "--get-tables") var_dump($database->createSchemaManager()->listTables());
    }
}