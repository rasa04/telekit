<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table as SchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Params extends Command
{
    protected function configure(): void
    {
        $this->setName('database:config')->setDescription('Database config');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $config = Capsule::connection()->getConfig();
        $table = new SchemaTool($output);
        $table
            ->setHeaders(array_keys($config))
            ->setRows([array_values($config)])
            ->render();
        return Command::SUCCESS;
    }
}