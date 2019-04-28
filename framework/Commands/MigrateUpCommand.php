<?php

namespace Amber\Framework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amber\Framework\DataMapper\Migration\Migration;
use Amber\Framework\Container\Application;
use Amber\Framework\Container\Facades\Filesystem;
use Amber\Phraser\Phraser;
use Illuminate\Database\Schema\Builder as Schema;

class MigrateUpCommand extends Command
{
    protected static $defaultName = 'migrate:up';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running migrations up.');

        $migrations = Filesystem::listContents('database/migrations');

        foreach ($migrations as $migration) {
            $fullname = preg_replace("/[0-9]/", '', $migration['basename']);
            $fullname = str_replace('.php', '', $fullname);
            $class = Phraser::fromSnakeCase($fullname)->toCamelCase();

            $message = $class->fromCamelCase()->toSnakeCase()->replace('_', ' ')->upperCaseFirst();

            $output->writeln("{$message} in process.");
            Application::make($class)->up(Application::get(Schema::class));
            $output->writeln("{$message} done.");
        }
    }
}
