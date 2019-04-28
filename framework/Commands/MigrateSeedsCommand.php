<?php

namespace Amber\Framework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Schema\Builder as Schema;
use Amber\Framework\Container\Application;

class MigrateSeedsCommand extends Command
{
    protected static $defaultName = 'migrate:seeds';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running seeds.');

        $seeds = Application::make(\DatabaseSeeder::class)->seeds();

        foreach ($seeds as $seed) {
            $output->writeln("Seeding {$seed} in progress.");
            Application::make($seed)->run();
            $output->writeln("Seeding {$seed} done.");
        }
    }
}
