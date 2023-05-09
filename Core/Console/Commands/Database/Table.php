<?php

namespace Core\Console\Commands\Database;

use Core\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table as SchemaTool;

class Table extends Command
{
    protected function configure(): void
    {
        $this->setName('database:table')
            ->setDescription('Describe table')
            ->addArgument('table');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new Database;

        $tableName = $input->getArgument('table');
        $columns = Capsule::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);
        if (!empty($columns)) {
            $data = [];
            foreach ($columns as $column) {
                $data[] = [$column->getName(), $column->getLength(), $column->getNotnull(), $column->getUnsigned()];
            }

            $table = new SchemaTool($output);
            $table
                ->setHeaders(['Name', 'Length', 'NotNull', 'Unsigned'])
                ->setRows($data)
                ->render();
        }
        else {
            $output->getFormatter()->setStyle("red-bg", new OutputFormatterStyle('white', "red"));
            $output->writeln("<red-bg>NO SUCH A TABLE</red-bg>");
        }
        return Command::SUCCESS;
    }
}