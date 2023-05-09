<?php
namespace Core\Database;

use Core\Env;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    use Env;

    public function __construct()
    {
        $capsule = new Capsule;
        if ($this->env("DB_DRIVER") === "sqlite") {
            $capsule->addConnection([
                "driver" => $this->env("DB_DRIVER"),
                "database" => __DIR__ . "/../../database/database.sqlite",
            ]);
        }
        elseif (in_array($this->env("DB_DRIVER"), ["mysql", "pgsql"])) {
            $capsule->addConnection([
                'driver' => $this->env("DB_DRIVER"),
                'host' => $this->env("DB_HOST") ?? 'localhost',
                'database' => $this->env("DATABASE"),
                'username' => $this->env("DB_USER"),
                'password' => $this->env("DB_PASSWORD"),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ]);
        }
        else{
            echo "The system is unable to support any other DBMS apart from SQLite, MySQLi or Postgres.";
        }

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}