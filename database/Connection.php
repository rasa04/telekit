<?php
namespace Database;

use Core\Env;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Tools\DsnParser;

class Connection
{
    use Env;
    /**
     * @throws Exception
     */
    public function __construct()
    {
        if ($this->env("DRIVER") === "sqlite") {
            $dsnParser = new DsnParser();
            $connectionParams = $dsnParser->parse('pdo-sqlite:///D:web/bots/rick/database/database.sqlite');
            $conn = DriverManager::getConnection($connectionParams);
        } else {
            echo "ERROR";
            die();
        }
    }
}