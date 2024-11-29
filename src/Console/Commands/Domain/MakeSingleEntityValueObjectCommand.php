<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Domain;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeSingleEntityValueObjectCommand extends GeneratorCommand
{

    protected $name = 'ddd:make-single-entity-value-object';
    protected $description = 'Create a new value object';
    protected $type = 'ValueObject';

    protected function getStub(): bool|array|string|null
    {
        return $this->option('stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Core' . $this->option('subfolderNamespace') . '\\' . $this->option(
                'modelName'
            ) . '\\Domain\\ValueObjects';
    }

    protected function getOptions(): array
    {
        return [
            ['stub', null, InputOption::VALUE_REQUIRED, 'The stub file to use', null],
            ['subfolderNamespace', null, InputOption::VALUE_REQUIRED, 'The subfolder namespace', null],
            ['column', null, InputOption::VALUE_REQUIRED, 'The column name', null],
            ['modelName', null, InputOption::VALUE_REQUIRED, 'The model name', null],
            ['maxLength', null, InputOption::VALUE_OPTIONAL, 'The max length', null],
            ['default', null, InputOption::VALUE_OPTIONAL, 'The default value', null],
        ];
    }

    protected function getNameInput(): string
    {
        return $this->option('modelName') . $this->option('column');
    }

    protected function replacePlaceholders(): void
    {
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));

        $this->files->put(
            $path,
            str_replace(
                ['DummyEntity', 'DummyColumn', 'DummyMaxLength'],
                [
                    $this->option('modelName'),
                    $this->option('column'),
                    $this->option('maxLength'),
                ],
                $this->files->get($path)
            )
        );
    }

    public function handle(): void
    {
        parent::handle();
        $this->replacePlaceholders();
    }

}
