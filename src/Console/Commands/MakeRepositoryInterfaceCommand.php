<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Elseoclub\RepositoryPattern\Console\Commands\Traits\FindModelTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeRepositoryInterfaceCommand extends GeneratorCommand
{
    use FindModelTrait;

    protected $name = 'repository-pattern:interface';
    protected $description = 'Creates the base interface for a repository.';
    protected $type = 'Repository interface';

    public function __construct (Filesystem $files)
    {
        parent::__construct($files);
        $this->files = $files;
        $this->createBaseRepositoryInterface();
    }

    protected function getStub (): string
    {
        return __DIR__ . '/../../../stubs/interfacev2.stub';
    }

    protected function getDefaultNamespace ($rootNamespace): string
    {
        return $rootNamespace . '\\Repositories\\Interfaces' . $this->subfolderNamespace;
    }

    protected function getNameInput (): string
    {

        return parent::getNameInput() . 'Interface';
    }

    protected function replaceModelPlaceholder ($model): void
    {
        $modelClass = $this->qualifyClass($model);
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));

        $this->files->put($path, str_replace(
            ['DummyModelUse', 'DummyMethods', 'DummyModel'],
            [$this->modelNamespace . '\\' . $this->modelName, $this->generateMethods(), $this->modelName],
            $this->files->get($path)
        ));
    }

    protected function generateMethods (): string
    {
        $methodsConfig = config('repository.interfaces', []);
        $methods = [];

        foreach ($methodsConfig as $method) {
            $methods[] = "    public function {$method['name']}():{$method['return']};";
        }

        return implode("\n", $methods);
    }

    public function handle (): void
    {
        $this->findModelNameAndNamespace($this->argument('name'));
        $this->input->setArgument('name', $this->modelName);
        parent::handle();
        $this->replaceModelPlaceholder($this->getNameInput());

    }

    private function createBaseRepositoryInterface (): void
    {
        $command = new MakeBaseRepositoryInterfaceCommand($this->files);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->setLaravel(app());
        $command->run($input, $output);
    }
}
