<?php

namespace Elseoclub\RepositoryPattern\Providers;

use Elseoclub\RepositoryPattern\Console\Commands\Domain\MakeEntityCommand;
use Elseoclub\RepositoryPattern\Console\Commands\Domain\MakeEntityValueObjectsCommand;
use Elseoclub\RepositoryPattern\Console\Commands\Domain\MakeSingleEntityValueObjectCommand;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryCommand;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryInterfaceCommand;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryV2Command;
use Elseoclub\RepositoryPattern\Console\Commands\MakeUseCaseCommand;
use Elseoclub\RepositoryPattern\Console\Commands\RepositoryBindCommand;
use Elseoclub\RepositoryPattern\Console\Commands\Shared\MakeSharedFilesCommand;
use Illuminate\Support\ServiceProvider;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                MakeRepositoryV2Command::class,
                MakeUseCaseCommand::class,
                RepositoryBindCommand::class,
                MakeRepositoryInterfaceCommand::class,
                MakeEntityCommand::class,
                MakeSharedFilesCommand::class,
                MakeEntityValueObjectsCommand::class,
                MakeSingleEntityValueObjectCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../../config/repository.php' => config_path('repository.php'),
            ], 'repository-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/repository.php',
            'repository'
        );
    }
}
