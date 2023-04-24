<?php
namespace Core\Database;

use Core\Env;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class Database
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
            try {
                $this->connection = DriverManager::getConnection([
                    'driver' => 'pdo_sqlite',
                    'path' => __DIR__ . "/../../database/database.sqlite",
                ]);
            }
            catch (\Exception $e) {
                exit("CANNOT CONNECT TO DATABASE: " . $e);
            }

            $this->schemaManager = $this->connection->createSchemaManager();
            $this->queryBuilder = $this->connection->createQueryBuilder();
        } else {
            exit("ERROR: other DBMS then sqlite are not supported");
        }
    }

    public function tables(): array
    {
        try {
            $tables = $this->connection->createSchemaManager()->listTables();
            return array_map(fn ($table) => $table->getName(), $tables);
        }
        catch (Exception $error) {
            exit($error);
        }
    }

    public function table(string $table, $column = '*'): array
    {
        try {
            return $this->connection
                ->createQueryBuilder()
                ->select($column)
                ->from($table)
                ->fetchAllAssociative();
        }
        catch (Exception $error) {
            exit($error);
        }
    }
}