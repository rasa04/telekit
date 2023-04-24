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

    private static string $table;
    private static string $alias;
    private static string $column;
    private static array $where;

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

    public static function table(string $table, string $column = '*', string $alias = ''): Database
    {
        static::$table = $table;
        static::$column = $column;
        static::$alias = $alias;
        return new static;
    }

    public static function column($column = '*'): Database
    {
        static::$column = $column;
        return new static;
    }

    public static function where(string $predicates, string $key, string|int $value): Database
    {
        self::$where['predicates'] = $predicates;
        self::$where['key'] = $key;
        self::$where['value'] = $value;
        return new static;
    }

    public function get(): array
    {
        try {
            if (isset(self::$where['predicates']) && isset(self::$where['key']) && isset(self::$where['value'])) {
                if (isset(self::$alias)) {
                    return $this->connection
                        ->createQueryBuilder()
                        ->select(static::$column)
                        ->from(static::$table, static::$alias)
                        ->where(static::$where['predicates'])
                        ->setParameter(static::$where['key'], static::$where['value'])
                        ->fetchAllAssociative();
                } else {
                    return $this->connection
                        ->createQueryBuilder()
                        ->select(static::$column)
                        ->from(static::$table)
                        ->where(static::$where['predicates'])
                        ->setParameter(static::$where['key'], static::$where['value'])
                        ->fetchAllAssociative();
                }
            } else {
                if (self::$alias !== '') {
                    return $this->connection
                        ->createQueryBuilder()
                        ->select(static::$column)
                        ->from(static::$table, static::$alias)
                        ->fetchAllAssociative();
                } else {
                    return $this->connection
                        ->createQueryBuilder()
                        ->select(static::$column)
                        ->from(static::$table)
                        ->fetchAllAssociative();
                }
            }
        }
        catch (Exception $error) {
            exit($error);
        }
    }

    public function update(string $data, string $column = ''): bool
    {
//        if (strlen($column) > 1) static::$column = $column;
        try {
            $this->queryBuilder
                ->update(static::$table)
                ->set(static::$column, ':new_value')
                ->where(static::$where['predicates'])
                ->setParameters([
                    'new_value'=> $data,
                    static::$where['key'] => static::$where['value']
                ])
                ->executeStatement();
        }
        catch (Exception $error) {
            exit($error);
        }
        return true;
    }
}