<?php

namespace Amber\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Amber\Helpers\ClassMaker\Builder as ClassBlueprint;
use Amber\Helpers\ClassMaker\Method;
use Amber\Helpers\ClassMaker\MethodArgument;
use Amber\Container\Facades\Filesystem;
use Amber\Container\Facades\Str as Phraser;
use Amber\Phraser\Base\StringArray\StringArray;

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

    protected function makeClass(string $name, StringArray $array, array $methods = [])
    {
        $name = $array->toCamelCase();

        $file = $array->toSnakeCase()
            ->append('.php')
            ->prepend(Carbon::now()->format('Ymdhis') . '_')
        ;

        $class = (new ClassBlueprint())
            ->setName($name)
            ->addInclude('Illuminate\Database\Schema\Builder as Schema')
            ->setMethods($methods)
        ;

        Filesystem::write("database/migrations/{$file}", $class->toString());
    }

    protected function create(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getOption('create');

        $output->writeln("Create {$table} table migration in process.");

        $raw = Phraser::fromSnakeCase($table)
            ->prepend('create')
            ->append('table')
        ;

        $upMethod = Phraser::new(Phraser::tab(2) . '$schema->create(\'' . $table . '\', function ($table) {')
            ->eol()
            ->append(Phraser::tab(3) . '$table->bigIncrements(\'id\');')
            ->eol()
            ->append(Phraser::tab(2) . '});')
        ;

        $downMethod = Phraser::new('        $schema->dropIfExists(\'' . $table . '\');');

        $methods[] = new Method('up', [new MethodArgument('schema', 'Schema')], 'public', null, $upMethod);
        $methods[] = new Method('down', [new MethodArgument('schema', 'Schema')], 'public', null, $downMethod);

        $this->makeClass($table, $raw, $methods);

        $output->writeln("<info>Create {$table} table migration done.</info>");
    }

    protected function alter(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getOption('alter');

        $output->writeln("Alter {$table} table migration in process.");

        $raw = Phraser::fromSnakeCase($table)
            ->prepend('alter')
            ->append('table')
        ;

        $upMethod = Phraser::new('        $schema->table(\'' . $table . '\', function ($table) {')
            ->eol()
            ->append('            //')
            ->eol()
            ->append('        });')
        ;

        $methods[] = new Method('up', [new MethodArgument('schema', 'Schema')], 'public', null, $upMethod);
        $methods[] = new Method('down', [new MethodArgument('schema', 'Schema')], 'public');

        $this->makeClass($table, $raw, $methods);

        $output->writeln("<info>Alter {$table} table migration done.</info>");
    }

    protected function empty(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('name') ?? Carbon::now()->format('Ymdhis');

        $output->writeln("Migration {$argument} in process.");

        $raw = Phraser::new($argument)
            ->prepend('migration_')
            ->fromSnakeCase()
        ;

        $methods[] = new Method('up', [new MethodArgument('schema', 'Schema')]);
        $methods[] = new Method('down', [new MethodArgument('schema', 'Schema')]);

        $this->makeClass($argument, $raw, $methods);

        $output->writeln("<info>Migration {$argument} done.</info>");
    }
}
