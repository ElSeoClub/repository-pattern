<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeUseCaseCommand extends GeneratorCommand
{
    protected $name = 'make:use-case';

    protected $description = 'Create a new use case';

    protected $type = 'Use Case';

    private string $subfolderNamespace;

    public function __construct (Filesystem $files)
    {
        parent::__construct($files);
        $this->files = $files;
    }

    protected function getStub (): string
    {
        return __DIR__ . "/../../../stubs/useCase.stub";
    }

    protected function getDefaultNamespace ($rootNamespace): string
    {
        return $rootNamespace . '\\UseCases' . $this->subfolderNamespace . '\\' . $this->option('model');
    }

    protected function getOptions (): array
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model that the use case is related to.'],
        ];
    }

    public function handle (): void
    {
        if (!$this->option('model')) {
            $this->error('The --model option is required.');

            exit(1);
        }
        $this->findModelNameAndNamespace($this->option('model'));
        parent::handle();
        if ($model = $this->option('model')) {
            $this->replaceModelPlaceholder($model);
        } else {
            $this->error("Don't forget to bind the repository for your Use Case in your Use Case constructor.");
        }
    }

    protected function replaceModelPlaceholder ($model): void
    {
        $modelClass = $this->qualifyClass($model);
        $modelInterface = $model . 'Interface';
        $modelInterfaceUse = 'App\\Repositories\\Interfaces' . $this->subfolderNamespace . '\\' . $model . 'Interface';
        $path = $this->getPath($this->qualifyClass($this->getNameInput()));

        $this->files->put($path, str_replace(
            ['DummyModelInterfaceUse', 'DummyModelInterface', 'DummyModel'],
            [$modelInterfaceUse, $modelInterface, $modelClass],
            $this->files->get($path)
        ));
    }

    protected function findModelNameAndNamespace (string $model): void
    {
        $namespace = 'App\\Models';

        if (class_exists("$namespace\\$model")) {
            $this->subfolderNamespace = '';

            return;
        }

        $baseDir = app_path('Models');

        if (!is_dir($baseDir)) {
            $this->error('The directory app/Models does not exist.');

            exit(2);
        }

        $files = $this->files->allFiles($baseDir);

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $className = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $fullClass = "$namespace\\$className";

            if (class_exists($fullClass) && strtolower(class_basename($fullClass)) === strtolower($model)) {
                $subfolder = dirname($relativePath);
                $subfolder = $subfolder === '.' ? '' : $subfolder;
                $this->subfolderNamespace = !empty($subfolder) ? '\\' . str_replace('/', '\\', $subfolder) : '';

                return;
            }
        }
        $this->error("Model $model not found.");
        exit(3);
    }

    protected function getNameInput (): string
    {
        $name = parent::getNameInput();
        $model = $this->option('model');

        if ($model) {
            return $name . $model . 'UseCase';
        }

        return $name;
    }
}
