<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Traits;

trait FindModelTrait
{
    protected string $modelNamespace;
    protected string $modelName;
    protected string $subfolderNamespace;
    protected string $subfolder;

    protected function findModelNameAndNamespace (string $model): void
    {
        $namespace = 'App\\Models';

        if (class_exists("$namespace\\$model")) {
            $this->modelNamespace = $namespace;
            $this->modelName = $model;
            $this->subfolderNamespace = '';
            $this->subfolder = '';

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

                $this->modelName = class_basename($fullClass);

                $subfolder = dirname($relativePath);
                $subfolder = $subfolder === '.' ? '' : $subfolder;
                $this->subfolder = $subfolder;
                $this->subfolderNamespace = !empty($subfolder) ? '\\' . str_replace('/', '\\', $subfolder) : '';
                $this->modelNamespace = $namespace . $this->subfolderNamespace;

                return;
            }
        }
        $this->error("Model $model not found.");
        exit(3);
    }
}
