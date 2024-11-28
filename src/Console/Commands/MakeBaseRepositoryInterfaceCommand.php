<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeBaseRepositoryInterfaceCommand extends GeneratorCommand
{

    protected $name = 'repository-pattern:base-interface';
    protected $description = 'Creates the base interface for a repository.';
    protected $type = 'Repository interface';

    protected function getStub (): string
    {
        return __DIR__ . '/../../../stubs/BaseRepositoryInterface.stub';
    }

    protected function getDefaultNamespace ($rootNamespace): string
    {
        return $rootNamespace . '\\Repositories\\Interfaces';
    }

    protected function getNameInput (): string
    {
        return 'BaseRepositoryInterface';
    }

    protected function getArguments (): array
    {
        return [];
    }

}
