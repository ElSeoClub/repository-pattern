<?php

namespace Elseoclub\RepositoryPattern\Providers;

use Elseoclub\RepositoryPattern\Console\Commands\RepositoryBindCommand;
use Illuminate\Support\ServiceProvider;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryCommand;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    public function boot (): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                RepositoryBindCommand::class
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
