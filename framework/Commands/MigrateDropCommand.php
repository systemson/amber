<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amber\Container\Facades\Gemstone;
use Aura\Sql;
use Aura\SqlSchema\ColumnFactory;
use Aura\SqlSchema\MysqlSchema; // for MySQL
use Aura\SqlSchema\PgsqlSchema; // for PostgreSQL
use Aura\SqlSchema\SqliteSchema; // for Sqlite
use Aura\SqlSchema\SqlsrvSchema; // for Microsoft SQL Server
use PDO;
use Amber\Container\Application;

class MigrateDropCommand extends Command
{
    protected static $defaultName = 'migrate:drop';

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $mediator = Gemstone::getMediator('sql');
        $pdo = $mediator->pdo;

        // a column definition factory
        $column_factory = new ColumnFactory();

        // the schema discovery object
        $schema = new PgsqlSchema($pdo, $column_factory);

        $tables = $schema->fetchTableList('public');

        foreach ($tables as $table) {
            $output->writeln("Droping table {$table} in process.");
            $drop = "DROP TABLE {$table} CASCADE;";
            $pdo->perform($drop);
            $output->writeln("<info>Droping table {$table} done.</info>");
        }
    }
}
