<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Amber\Helpers\ClassMaker\ClassBlueprint;
use Amber\Phraser\Phraser;
use Amber\Container\Facades\Filesystem;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->addOption('create', 'c', InputOption::VALUE_REQUIRED, 'The migration table to create.', false)
            ->addOption('alter', 'a', InputOption::VALUE_REQUIRED, 'The migration table to modify.', false)
            ->addArgument('name', InputArgument::OPTIONAL, 'The migration name, in snake case format.')
        ;
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('create') !== false) {
            $this->create($input, $output);
        } elseif ($input->getOption('alter') !== false) {
            $this->alter($input, $output);
        } else {
            $this->empty($input, $output);
        }
    }

    protected function create(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getOption('create');

        $output->writeln("Create {$table} table migration in process.");

        $raw = Phraser::make($table)
            ->prepend('_create_')
            ->append('_table')
            ->fromSnakeCase()
        ;

        $name = $raw->toCamelCase();

        $file = $raw->toSnakeCase()
            ->append('.php')
            ->prepend(Carbon::now()->format('Ymdhis') . '_')
        ;

        $upMethod = Phraser::make('        $schema->create(\'' . $table . '\', function ($table) {')
            ->eol()
            ->append('            //')
            ->eol()
            ->append('        });')
        ;

        $downMethod = Phraser::make('        $schema->dropIfExists(\'' . $table . '\');');

        $class = (new ClassBlueprint())
            ->setName($name)
            ->addInclude('Illuminate\Database\Schema\Builder as Schema')
            ->addMethod('up', ['schema' => ['type' => 'Schema']], 'public', null, $upMethod)
            ->addMethod('down', ['schema' => ['type' => 'Schema']], 'public', null, $downMethod)
        ;

        Filesystem::write("database/migrations/{$file}", $class->toString());

        $output->writeln("Create {$table} table migration done.");
    }

    protected function alter(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getOption('alter');

        $output->writeln("Alter {$table} table migration in process.");

        $raw = Phraser::make($table)
            ->prepend('_alter_')
            ->append('_table')
            ->fromSnakeCase()
        ;

        $name = $raw->toCamelCase();

        $file = $raw->toSnakeCase()
            ->append('.php')
            ->prepend(Carbon::now()->format('Ymdhis') . '_')
        ;

        $upMethod = Phraser::make('        $schema->table(\'' . $table . '\', function ($table) {')
            ->eol()
            ->append('            //')
            ->eol()
            ->append('        });')
        ;

        $downMethod = Phraser::make('        $schema->dropIfExists(\'' . $table . '\');');

        $class = (new ClassBlueprint())
            ->setName($name)
            ->addInclude('Illuminate\Database\Schema\Builder as Schema')
            ->addMethod('up', ['schema' => ['type' => 'Schema']], 'public', null, $upMethod)
            ->addMethod('down', ['schema' => ['type' => 'Schema']], 'public', null, $downMethod)
        ;

        Filesystem::write("database/migrations/{$file}", $class->toString());

        $output->writeln("Alter {$table} table migration done.");
    }

    protected function empty(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('name') ?? 'custom_'.Carbon::now()->format('Ymdhis');

        $output->writeln("Migration {$argument} in process.");

        $raw = Phraser::make($argument)
            ->prepend('migration_')
            ->fromSnakeCase()
        ;

        $name = $raw->toCamelCase();

        $file = $raw->toSnakeCase()
            ->append('.php')
            ->prepend(Carbon::now()->format('Ymdhis') . '_')
        ;

        $class = (new ClassBlueprint())
            ->setName($name)
            ->addInclude('Illuminate\Database\Schema\Builder as Schema')
            ->addMethod('up', ['schema' => ['type' => 'Schema']], 'public')
            ->addMethod('down', ['schema' => ['type' => 'Schema']], 'public')
        ;

        Filesystem::write("database/migrations/{$file}", $class->toString());

        $output->writeln("Migration {$argument} done.");
    }
}
