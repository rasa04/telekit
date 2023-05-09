<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table as SchemaTool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Tables extends Command
{
    protected function configure(): void
    {
        $this->setName('database:tables')->setDescription('List tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $tables = Capsule::connection()->getDoctrineSchemaManager()->listTableNames();
        if (empty($tables)) {
            $output->getFormatter()->setStyle("blue-bg", new OutputFormatterStyle('white', "blue"));
            $output->writeln("<blue-bg>NO TABLES YET</blue-bg>");
        }
        else {
            $list = [];
            foreach ($tables as $table) $list[] = [$table];
            $table = new SchemaTool($output);
            $table
                ->setHeaders(["TABLES"])
                ->setRows($list)
                ->render();
        }
        return Command::SUCCESS;
    }
}