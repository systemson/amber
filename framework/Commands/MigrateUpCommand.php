<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amber\DataMapper\Migration\Migration;
use Amber\Container\Application;
use Amber\Container\Facades\Filesystem;
use Amber\Phraser\Phraser;
use Illuminate\Database\Schema\Builder as Schema;

class MigrateUpCommand extends Command
{
    protected static $defaultName = 'migrate:up';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running migrations up.</comment>');

        $migrations = Filesystem::listContents('database/migrations');

        foreach ($migrations as $migration) {
            $fullname = preg_replace("/[0-9]/", '', $migration['basename']);
            $fullname = str_replace('.php', '', $fullname);
            $class = Phraser::fromSnakeCase($fullname)->toCamelCase();

            $message = $class->fromCamelCase()->toSnakeCase()->replace('_', ' ')->upperCaseFirst();

            $output->writeln("{$message} in process.");
            Application::make($class)->up(Application::get(Schema::class));
            $output->writeln("<info>{$message} done.</info>");
        }
    }
}
