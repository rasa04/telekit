<?php

namespace Core\Database;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema as DoctrineSchema;
use Doctrine\DBAL\Schema\SchemaException;

class Schema
{
    /**
     * @throws SchemaException
     * @throws Exception
     */
    static public function create(string $name, callable $table_description): void
    {
        $schema = (new DoctrineSchema);
        $table = $schema->createTable($name);

        $table_blueprint = new Blueprint();
        call_user_func($table_description, $table_blueprint);

        if ($table_blueprint->columns["id"]) {
            $table->addColumn("id", "integer", [
                "unsigned" => true,
                "autoincrement" => true,
                "notnull" => true
            ]);
            $table->setPrimaryKey(["id"]);
        }
        foreach ($table_blueprint->columns["integer"] as $column) {
            $table->addColumn($column, "integer", [
                "unsigned" => false
            ]);
        }
        foreach ($table_blueprint->columns["string"] as $column) {
            $table->addColumn($column, "string", []);
        }
        foreach ($table_blueprint->columns["json"] as $column) {
            $table->addColumn($column, "json", []);
        }

        $conn = (new Database)->connection;
        $sql = $schema->toSql($conn->getDatabasePlatform());
        foreach ($sql as $statement) {
            try {
                $result = $conn->executeStatement($statement);
                echo $result . "\n";
            }
            catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    }

    public static function dropIfExist(string $name): void
    {
        try {
            $result = (new Database)->connection->executeStatement("DROP TABLE IF EXISTS $name");
            echo $result . "\n";
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }
}