<?php
namespace Database;

use Core\Env;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class Connect
{
    use Env;

    public Connection $connection;
    public AbstractSchemaManager $schemaManager;
    public QueryBuilder $queryBuilder;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if ($this->env("DB_DRIVER") === "sqlite") {
            $this->connection = DriverManager::getConnection([
                'driver' => 'pdo_sqlite',
                'path' => $this->app_path() . "\database\database.sqlite",
            ]);

            $this->schemaManager = $this->connection->createSchemaManager();
            $this->queryBuilder = $this->connection->createQueryBuilder();
        } else {
            echo "ERROR: other DBMS then sqlite are not supported";
            die();
        }
    }
}