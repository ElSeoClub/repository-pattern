<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Domain;

use Elseoclub\RepositoryPattern\Console\Commands\Traits\DoctrineToEntityTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeEntityValueObjectsCommand extends Command
{
    use DoctrineToEntityTrait;

    protected $signature = 'ddd:make-entity-value-objects {--entity=} ';
    protected $description = 'Create a new Entity Value Objects';

    public function handle(): void
    {
        $this->findModelNameAndNamespace($this->option('entity'));

        $modelColumns = $this->getTableColumnsWithDetails();

        if($this->option('entity')) {
            foreach($modelColumns as $column => $details) {
                $this->makeValueObject($column, $details);
            }
        }
        else {
            throw new \RuntimeException('Please provide the entity name');
        }
    }

    public function makeValueObject(string $column, array $details): void
    {
        $stubPath = __DIR__ . '/../../../../stubs/Domain/ValueObjects/';

        if($column === 'id' && $details['type'] === 'BIGINT') {
            $stub = 'IdValueObject.stub';
        }
        elseif($column === 'id' && $details['type'] === 'CHAR' && $details['length'] === 36) {
            $stub = 'UuidValueObject.stub';
        }
        elseif($details['type'] === 'VARCHAR') {
            $stub = 'VarcharValueObject.stub';
        }
        else {
            $stub = 'VarcharValueObject.stub';
        }
        $command = new MakeSingleEntityValueObjectCommand(new Filesystem());
        $input   = new ArrayInput([
            'name'                 => $this->modelName,
            '--stub'               => $stubPath . $stub,
            '--subfolderNamespace' => $this->subfolderNamespace,
            '--column'             => Str::studly($column),
            '--modelName'          => $this->modelName,
            '--maxLength'          => $this->getRealMaxLenght($details['type'], $details['length']),
            '--default'            => $details['default'],
        ]);
        $output  = new NullOutput();

        $command->setLaravel(app());
        $command->run($input, $output);
    }

    protected function getRealMaxLenght(string $type, ?int $length): int
    {
        if($length === null) {
            if($type === 'VARCHAR') {
                return 255;
            }
            elseif($type === 'BIGINT') {
                return 20;
            }
            elseif($type === 'INT') {
                return 11;
            }
        }

        return $length;
    }

}
