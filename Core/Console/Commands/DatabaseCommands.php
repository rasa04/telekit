<?php

namespace Core\Console\Commands;

use Core\Database\Database;
use Core\Env;
use Core\Validator\ErrorHandler;
use Database\seeders\DatabaseSeeder;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;

class DatabaseCommands
{
    use Env;

    public function __construct($options, $argv)
    {
        new Database;
        if (!isset($argv[2])) {
            var_dump(Capsule::connection()->getDatabaseName());
        }
        elseif ($argv[2] === '--params') {
            var_dump(Capsule::connection()->getConfig());
        }
        elseif ($argv[2] === '--driver') {
            var_dump(Capsule::connection()->getDriverName());
        }
        elseif ($argv[2] === '--version') {
            var_dump(Capsule::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION));
        }
        elseif (isset($options['table'])) {
            $table = $options['table'];
            $columns = Capsule::connection()->getDoctrineSchemaManager()->listTableColumns($table);
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
                    Capsule::connection()->getDoctrineSchemaManager()->listTableColumns($table)
                );
                if (!in_array($options['column'], $columns)) exit("NO SUCH A COLUMN");

               $result = Capsule::table($table)->pluck($options['column']);
            }else {
                $result = Capsule::table($table)->get()->values();
            }
            var_dump($result);
        }
        elseif ($argv[2] === '--tables') {
            $tables = Capsule::connection()->getDoctrineSchemaManager()->listTableNames();
            if (empty($tables)) echo "NO TABLES YET";
            foreach ($tables as $table) {
                echo $table . "\n";
            }
        }
        elseif ($argv[2] === '--migrate') {
            $migrations_folder = $this->app_path() . "\database\migrations\\";
            $folders = scandir($migrations_folder);
            $migrations = array_filter($folders, fn($files) => preg_match("#^\d#", $files));

            foreach ($migrations as $migration)
            {
                $migration = require $migrations_folder . $migration;
                if (!isset($argv[3])) $migration->up();
                elseif ($argv[3] === '--fresh') $migration->down();
            }
        }
        elseif ($argv[2] === '--seed') {
            $seeder = new DatabaseSeeder();
            $seeder->run();
        }
        else {
            new ErrorHandler("UNKNOWN COMMAND: " . $argv[2]);
        }
    }
}