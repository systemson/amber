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
use Amber\Collection\Collection;

class MigrateUpCommand extends Command
{
    protected static $defaultName = 'migrate:up';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running migrations up.</comment>');

        $migrations = Collection::make(
                Filesystem::listContents('database/migrations')
            )->sort(function ($a, $b) {
                return $a['filename'] <=> $b['filename'];
            })
        ;

        foreach ($migrations as $migration) {
            $class = $this->getClassName($migration['basename']);

            $message = $this->getMessage($class)
                ->upperCaseFirst()
            ;

            $output->writeln("{$message} in process.");
            Application::make($class)->up(Application::get(Schema::class));
            $output->writeln("<info>{$message} done.</info>");
        }
    }

    protected function getClassName($path)
    {
        return Phraser::make($path)
            ->pregReplace("/[0-9]/", '')
            ->remove('.php')
            ->fromSnakeCase()
            ->toCamelCase()
        ;
    }

    protected function getMessage($class)
    {
        return $class
            ->fromCamelCase()
            ->toSnakeCase()
            ->replace('_', ' ')
        ;
    }
}
