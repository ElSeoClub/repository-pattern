<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Domain;

use Elseoclub\RepositoryPattern\Console\Commands\Traits\DoctrineToEntityTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeEntityCommand extends GeneratorCommand
{
    use DoctrineToEntityTrait;

    protected $name = 'ddd:entity';
    protected $description = 'Create a new entity class';
    protected $type = 'Entity';

    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../../../stubs/Domain/Entity.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Core' . $this->subfolderNamespace . '\\' . $this->modelName . '\\Domain';
    }

    protected function getNameInput(): string
    {
        $name = $this->modelName;

        return $name . 'Entity';
    }

    protected function generateConstructorProperties(): string
    {
        $properties = '';

        foreach($this->getTableColumnsWithDetails() as $column => $details) {
            if($properties !== '') {
                $properties .= ', ';
            }

            if($details['default'] !== null) {
                if($details['type'] === 'INT' || $details['type'] === 'BIGINT' || $details['type'] === 'TINYINT' || $details['type'] === 'SMALLINT' || $details['type'] === 'MEDIUMINT' || $details['type'] === 'DECIMAL' || $details['type'] === 'FLOAT' || $details['type'] === 'DOUBLE' || $details['type'] === 'REAL' || $details['type'] === 'BIT' || $details['type'] === 'BOOLEAN' || $details['type'] === 'SERIAL' || $details['type'] === 'BIGSERIAL' || $details['type'] === 'NUMERIC') {
                    $default = ' = ' . $details['default'];
                }
                else {
                    $default = ' = \'' . $details['default'] . '\'';
                }
            }

            $properties .= 'private ' . $this->modelName . Str::studly($column) . ' $' . Str::camel(
                    $column
                ) . ($default ?? '');
        }

        return $properties;
    }

    protected function generatePropertiesUseStatements(): string
    {
        $useStatements = '';

        foreach($this->getTableColumnsWithDetails() as $column => $details) {
            $useStatements .= 'use ' . $this->rootNamespace(
                ) . 'Core' . $this->subfolderNamespace . '\\' . $this->modelName . '\\Domain\\ValueObjects\\' . $this->modelName . Str::studly(
                    $column
                ) . ';' . PHP_EOL;
        }

        return $useStatements;
    }

    protected function generatePropertiesGetters(): string
    {
        $properties = '';

        foreach($this->getTableColumnsWithDetails() as $column => $details) {
            $properties .= PHP_EOL . '    public function ' . Str::camel(
                    $column
                ) . '(): ' . $this->modelName . Str::studly($column) . PHP_EOL;
            $properties .= '    {' . PHP_EOL;
            $properties .= '        return $this->' . Str::camel($column) . ';' . PHP_EOL;
            $properties .= '    }' . PHP_EOL;
        }

        return $properties;
    }

    protected function replacePlaceholders(): void
    {
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));

        $this->files->put(
            $path,
            str_replace(
                ['DummyEntity', 'DummyUseProperties', 'DummyProperties', 'DummyGettersProperties'],
                [
                    $this->modelName . 'Entity',
                    $this->generatePropertiesUseStatements(),
                    $this->generateConstructorProperties(),
                    $this->generatePropertiesGetters(),
                ],
                $this->files->get($path)
            )
        );
    }

    public function handle(): void
    {
        $this->findModelNameAndNamespace($this->argument('name'));
        parent::handle();
        $this->replacePlaceholders();
    }

}
