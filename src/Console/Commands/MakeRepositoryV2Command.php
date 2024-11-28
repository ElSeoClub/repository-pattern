<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Elseoclub\RepositoryPattern\Console\Commands\Traits\FindModelTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class MakeRepositoryV2Command extends GeneratorCommand
{
    use FindModelTrait;

    protected $name = 'make:repositoryv2';
    protected $description = 'Creates a repository, interface, and updates the service provider for a model.';
    protected $type = 'Model repository';

    public function __construct (Filesystem $files)
    {
        parent::__construct($files);
        $this->files = $files;
    }

    protected function getStub (): string
    {
        return __DIR__ . '/../../../stubs/repositoryv2.stub';
    }

    protected function getDefaultNamespace ($rootNamespace): string
    {
        return $rootNamespace . '\\Repositories' . $this->subfolderNamespace;
    }

    public function handle (): void
    {
        $this->findModelNameAndNamespace($this->argument('name'));
        $this->input->setArgument('name', $this->modelName);
        parent::handle();
    }

    protected function getNameInput (): string
    {
        $name = $this->modelName;

        return $name . 'Repository';
    }
}
