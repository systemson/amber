<?php

namespace Amber\Container\Providers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Psr\Container\ContainerInterface;

class LoggerServiceProvider extends ServiceProvider
{
    public function boot(ContainerInterface $container): void
    {
        $container->register(LoggerInterface::class, Logger::class)
        ->setArgument('__construct', 'name', 'AmberFramework')
        ->afterConstruct('pushHandler', function () {
            switch (config('logger.driver')) {
                case 'simple':
                    return new StreamHandler(config('logger.path'));
                    break;

                case 'daily':
                    return new RotatingFileHandler(config('logger.path'), config('logger.max_files'));
                    break;
                
                default:
                    return new StreamHandler(config('logger.path'));
                    break;
            }
        });
    }
}
