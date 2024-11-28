<?php

namespace Elseoclub\RepositoryPattern\Providers;

use Elseoclub\RepositoryPattern\Console\Commands\base;
use Elseoclub\RepositoryPattern\Console\Commands\MakeBaseRepositoryInterfaceCommand;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryInterfaceCommand;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryV2Command;
use Elseoclub\RepositoryPattern\Console\Commands\MakeUseCaseCommand;
use Elseoclub\RepositoryPattern\Console\Commands\RepositoryBindCommand;
use Illuminate\Foundation\Console\InterfaceMakeCommand;
use Illuminate\Support\ServiceProvider;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryCommand;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    public function boot (): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                MakeRepositoryV2Command::class,
                MakeUseCaseCommand::class,
                RepositoryBindCommand::class,
                MakeRepositoryInterfaceCommand::class
            ]);

            $this->publishes([
                __DIR__ . '/../../config/repository.php' => config_path('repository.php'),
            ], 'repository-config');
        }
    }

    public function register (): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/repository.php',
            'repository'
        );
    }
}
