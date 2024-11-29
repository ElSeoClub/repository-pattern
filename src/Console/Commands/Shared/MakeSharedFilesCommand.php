<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Shared;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeSharedFilesCommand extends GeneratorCommand
{
    protected $name = 'ddd:make-shared-files';
    protected $description = 'Create a new shared file';
    protected $type = 'SharedFile';

    public function getStubs(): array
    {
        $files = $this->files->allFiles(__DIR__ . '/../../../../stubs/Shared');
        $stubs = [];

        foreach($files as $file) {
            $relativePath = str_replace(__DIR__ . '/../../../../stubs/Shared/', '', $file->getPathname());
            $directory    = dirname($relativePath) !== '.' ? dirname($relativePath) : null;
            $stubs[]      = [
                'filename'  => $file->getFilename(),
                'directory' => $directory,
            ];
        }

        return $stubs;
    }

    public function makeValueObject(string $stub, ?string $directory): void
    {
        $command = new MakeSingleSharedFileCommand(new Filesystem());

        $input  = new ArrayInput([
            'name'        => $stub,
            '--directory' => $directory,
        ]);
        $output = new NullOutput();

        $command->setLaravel($this->getLaravel() ?? app());
        $command->run($input, $output);
    }

    public function handle(): void
    {
        $stubs = $this->getStubs();

        foreach($stubs as $stub) {
            $directory = $stub['directory'] ?? null;
            $this->makeValueObject($stub['filename'], $directory);
        }
    }

    protected function getStub()
    {
        return null;
    }

    protected function getNameInput(): string
    {
        return 'Default';
    }

    protected function getArguments(): array
    {
        return [];
    }
}
