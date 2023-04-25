<?php

namespace Core\Console\Commands;

use Core\Database\Database;
use Core\Env;
use Core\Validator\ErrorHandler;
use Doctrine\DBAL\Exception;

class DatabaseCommands
{
    use Env;

    /**
     * @throws Exception
     */
    public function __construct($options, $argv)
    {
        $conn = (new Database)->connection;

        if (!isset($argv[2])) {
            var_dump($conn->getDatabase());
        }
        elseif ($argv[2] == '--connect') {
            var_dump($conn->connect());
        }
        elseif ($argv[2] === '--parameters') {
            var_dump($conn->getParams());
        }
        elseif ($argv[2] === '--version') {
            echo 'SQLite version: ' . $conn->fetchOne('SELECT sqlite_version()');
        }
        elseif (isset($options['table'])) {
            $table = $options['table'];
            $columns = $conn->createSchemaManager()->listTableColumns($table);
            echo "Name | Length | NotNull | Unsigned | \n-----------------------------------\n";
            foreach ($columns as $column) {
                echo ($column->getName() ?? "---") . ' | '
                    . ($column->getLength() ?? "---")  . ' | '
                    . (($column->getNotnull()) ? "true" : "false") . ' | '
                    . ($column->getUnsigned() ? "true" : "false or ---")  . ' | '
                    . "\n-----------------------------------\n";
            }
        }
        elseif (isset($options['showTable'])) {
            $table = $options['showTable'];
            if (isset($options['column'])) {
                $columns = array_map(
                    fn ($column) => $column->getName(),
                    $conn->createSchemaManager()->listTableColumns('users')
                );
                if (!in_array($options['column'], $columns)) exit("NO SUCH A COLUMN");
                $result = array_map(
                    fn ($row) => $row[$options['column']],
                    $conn->createQueryBuilder()->select($options['column'])->from($table)->fetchAllAssociative()
                );
            }else {
                $result = $conn->createQueryBuilder()->select('*')->from($table)->fetchAllAssociative();
            }
            var_dump($result);
        }
        elseif ($argv[2] === '--tables') {
            $tables = $conn->createSchemaManager()->listTables();
            foreach ($tables as $table) {
                echo $table->getName() . "\n";
            }
        }
        elseif ($argv[2] === '--migrate') {
            $migrations_folder = $this->app_path() . "\database\migrations\\";
            $folders = scandir($migrations_folder);
            $migrations = array_filter($folders, fn($files) => preg_match("#^\d#", $files));

            if (!isset($argv[3])) {
                foreach ($migrations as $migration)
                {
                    $migration = require $migrations_folder . $migration;
                    $migration->up();
                }
            }
            elseif ($argv[3] === '--fresh') {
                foreach ($migrations as $migration)
                {
                    $migration = require $migrations_folder . $migration;
                    $migration->down();
                }
            }
        }
        else {
            new ErrorHandler("UNKNOWN COMMAND: " . $argv[2]);
        }
    }
}