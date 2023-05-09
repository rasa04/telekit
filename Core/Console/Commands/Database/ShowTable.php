<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table as SchemaTool;

class ShowTable extends Command
{
    protected function configure(): void
    {
        $this->setName('database:showTable')
            ->setDescription('show table contents')
            ->addArgument('table')
            ->addOption('column', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;

        $tableName = $input->getArgument('table');
        $column = $input->getOption('column');

        if (isset($column)) {
            $result = Capsule::table($tableName)->get()->map(function ($row) use ($column) {
                return [$row->$column];
            })->toArray();

            $table = new SchemaTool($output);
            $table
                ->setHeaders([$column])
                ->setRows($result)
                ->render();
        }
        else {
            $columns = Capsule::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);
            $columnsName = [];
            foreach ($columns as $column) {
                $columnsName[] = $column->getName();
            }

            $result = Capsule::table($tableName)->get()->map(function ($row) use ($columnsName) {
                $new_row = [];
                foreach ($columnsName as $column) {
                    $new_row[] = $row->$column;
                }
                return $new_row;
            })->toArray();

            $table = new SchemaTool($output);
            $table
                ->setHeaders($columnsName)
                ->setRows($result)
                ->render();
        }
        return Command::SUCCESS;
    }
}