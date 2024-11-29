<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Shared;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeSingleSharedFileCommand extends GeneratorCommand
{

    protected $name = 'ddd:make-single-shared-file';
    protected $description = 'Create a new shared file';
    protected $type = 'SharedFile';

    protected function getStub(): string
    {
        $directory     = $this->option('directory');
        $directoryPath = $directory ? $directory . '/' : '';

        return __DIR__ . '/../../../../stubs/Shared/' . $directoryPath . $this->getNameInput() . '.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        $directory     = $this->option('directory');
        $namespacePath = $directory ? '\\' . str_replace('/', '\\', trim($directory, '/')) : '';

        return $rootNamespace . '\Core\Shared' . $namespacePath;
    }

    protected function getNameInput(): bool|array|string|null
    {
        return str_replace('.stub', '', $this->argument('name'));
    }

    protected function getOptions(): array
    {
        return [
            [
                'directory',
                null,
                InputOption::VALUE_OPTIONAL,
                'The subdirectory inside Shared where the file resides',
                null,
            ],
        ];
    }

}
