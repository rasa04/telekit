<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table as SchemaTool;

class Table extends Command
{
    protected function configure(): void
    {
        $this->setName('database:table')
            ->setDescription('show table contents')
            ->addArgument('table')
            ->addOption('size', 's' )
            ->addOption('desc', 'd' )
            ->addOption('columns', 'c', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;
        $table = new SchemaTool($output);

        $tableName = $input->getArgument('table');
        $columns = $input->getOption('columns');
        $size = $input->getOption('size');
        $desc = $input->getOption('desc');

        if ($size) {
            $count = Capsule::table($tableName)->count();
            $table->setHeaders(['Rows quantity'])
                ->setRows([[$count]])
                ->render();
        }
        elseif ($desc) {
            $columns = Capsule::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);
            if (!empty($columns)) {
                $data = [];
                foreach ($columns as $column) {
                    $data[] = [
                        $column->getName(),
                        $column->getType()->getName(),
                        $column->getLength(),
                        $column->getDefault(),
                        $column->getAutoincrement() ? 'true' : 'false',
                        $column->getNotnull(),
                        $column->getUnsigned(),
                        $column->getScale(),
                    ];
                }

                $table
                    ->setHeaders([
                        'Name',
                        'Type',
                        'Length',
                        'Default',
                        'Autoincrement',
                        'NotNull',
                        'Unsigned',
                        'Scale',
                    ])
                    ->setRows($data)
                    ->render();
            }
            else {
                $output->getFormatter()->setStyle("red-bg", new OutputFormatterStyle('white', "red"));
                $output->writeln("<red-bg>NO SUCH A TABLE</red-bg>");
            }
        }
        elseif (!empty($columns)) {
            $result = Capsule::table($tableName)->get()->map(function ($row) use ($columns) {
                $temp = [];
                foreach ($columns as $column) {
                    $temp[] = $row->$column;
                }
                return $temp;
            })->toArray();

            $table
                ->setHeaders($columns)
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

            $table
                ->setHeaders($columnsName)
                ->setRows($result)
                ->render();
        }
        return Command::SUCCESS;
    }
}