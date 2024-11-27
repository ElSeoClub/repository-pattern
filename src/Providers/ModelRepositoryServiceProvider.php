<?php

namespace Elseoclub\RepositoryPattern\Providers;

use Illuminate\Support\ServiceProvider;
use Elseoclub\RepositoryPattern\Console\Commands\MakeRepositoryCommand;

class ModelRepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../../config/repository.php' => config_path('repository.php'),
            ], 'repository-config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/repository.php',
            'repository'
        );
    }
}
