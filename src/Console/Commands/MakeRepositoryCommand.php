<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {model}';
    protected $description = 'Creates a repository, interface, and updates the service provider for a model.';

    protected string $repositoryNamespace = 'App\\Repositories';
    protected ?string $modelNamespace = null;
    protected ?string $modelName = null;
    protected ?string $subfolder = null;
    protected ?string $subfolderNamespace = null;

    protected Filesystem $files;

    public function __construct (Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle (): void
    {
        $this->findModelNameAndNamespace($this->argument('model'));
        if (!$this->modelName || !$this->modelNamespace || $this->subfolderNamespace === null || $this->subfolder === null) {
            $this->error("Model {$this->argument('model')} not found");

            exit(1);
        }

        $this->createRepository();
        $this->createInterface();
        $this->updateProvider();

    }

    protected function createRepository (): void
    {

        $namespace = $this->repositoryNamespace . $this->subfolderNamespace;

        $interfaceNamespace = $this->repositoryNamespace . '\\Interfaces' . $this->subfolderNamespace;
        $path = app_path('Repositories/' . $this->subfolder . "/{$this->modelName}Repository.php");
        $directory = dirname($path);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if ($this->files->exists($path)) {
            $this->warn("The repository {$this->modelName}Repository already exists.");

            return;
        }

        $methods = $this->generateRepositoryMethods();

        $stub = $this->getStub('repository');
        $content = str_replace(
            ['{{namespace}}', '{{interfaceNamespace}}', '{{model_namespace}}', '{{methods}}', '{{model}}'],
            [$namespace, $interfaceNamespace, $this->modelNamespace, $methods, $this->modelName],
            $stub
        );

        $this->files->put($path, $content);
        $this->info("Repository created at {$path}");
    }

    protected function createInterface (): void
    {
        $namespace = $this->repositoryNamespace . '\\Interfaces' . $this->subfolderNamespace;
        $path = app_path('Repositories/Interfaces/' . $this->subfolder . "/{$this->modelName}Interface.php");
        $directory = dirname($path);
        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        if ($this->files->exists($path)) {
            $this->warn("The interface {$this->modelName}Interface already exists.");

            return;
        }

        $methods = $this->generateMethods();
        $stub = $this->getStub('interface');
        $content = str_replace(
            ['{{namespace}}', '{{methods}}', '{{model_namespace}}', '{{model}}'],
            [$namespace, $methods, $this->modelNamespace, $this->modelName],
            $stub
        );

        $this->files->put($path, $content);
        $this->info("Interface created at {$path}");
    }

    protected function updateProvider (): void
    {
        $interfaceClass = $this->repositoryNamespace . '\\Interfaces' . $this->subfolderNamespace . "\\{$this->modelName}Interface";
        $repositoryClass = $this->repositoryNamespace . $this->subfolderNamespace . "\\{$this->modelName}Repository";
        $repositoryCacheClass = null;
        if (config('repository.cache', false)) {
            $repositoryCacheClass = $this->repositoryNamespace . $this->subfolderNamespace . "\\{$this->modelName}CacheRepository";
        }

        $path = app_path('Providers/ModelRepositoryServiceProvider.php');

        $created = 'updated';
        $directory = dirname($path);

        if (!$this->files->exists($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $bindLine = "\$this->app->bind(\\{$interfaceClass}::class, \\{$repositoryClass}::class);";
        $bindCacheLine = "\$this->app->bind(\\{$interfaceClass}::class, \\{$repositoryCacheClass}::class);";

        $useBindLine = $bindLine;
        $wrongBindLine = $bindCacheLine;
        if (config('repository.cache', false)) {
            $useBindLine = $bindCacheLine;
            $wrongBindLine = $bindLine;
        }
        if (!$this->files->exists($path)) {
            $stub = $this->getStub('provider');
            $content = str_replace(
                ['{{bindings}}'],
                [$useBindLine],
                $stub
            );
            $this->files->put($path, $content);
            $created = 'created';
        }
        $content = $this->files->get($path);
        if (str_contains($content, $wrongBindLine)) {
            $content = str_replace($wrongBindLine, $useBindLine, $content);
            $this->files->put($path, $content);
            $this->info("Container Binding {$created} at {$path}");

            return;
        } elseif (!str_contains($content, $useBindLine)) {
            $bindPointer = '// [binds]';

            if (!str_contains($content, $bindPointer)) {
                $this->error("Cannot add the Container Binding.\nInside in app/Provider/ModelRepositoryServiceProvider \nPlease add the following line manually: \n\npublic function register()\n{\n    // [binds]\n}\n\nAnd then run the command again.");

                return;
            }

            $content = str_replace(
                $bindPointer,
                "{$bindPointer}\n        {$useBindLine}",
                $content
            );
            $this->files->put($path, $content);
            $this->info("Container Binding {$created} at {$path}");
        } else {
            $this->warn("The container bind for {$this->modelName} already exists in the provider.");
        }

    }

    protected function getStub (string $type): string
    {
        $stubPath = __DIR__ . "/../../../stubs/{$type}.stub";
        if (!$this->files->exists($stubPath)) {
            $this->error("The stub {$type}.stub does not exist.");
            exit;
        }

        return $this->files->get($stubPath);
    }

    protected function findModelNameAndNamespace (string $model): void
    {
        $namespace = 'App\\Models';

        if (class_exists("{$namespace}\\{$model}")) {
            $this->modelNamespace = $namespace;
            $this->modelName = $model;
            $this->subfolderNamespace = '';
            $this->subfolder = '';

            return;
        }

        $baseDir = app_path('Models');

        if (!is_dir($baseDir)) {
            $this->error("The directory app/Models does not exist.");

            exit(2);
        }

        $files = $this->files->allFiles($baseDir);

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $className = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $fullClass = "{$namespace}\\{$className}";

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
        $this->error("Model {$model} not found.");
        exit(3);
    }

    protected function generateMethods (): string
    {
        $methodsConfig = config('repository.interfaces', []);
        $methods = [];

        foreach ($methodsConfig as $method) {
            $parameters = array_map(function ($param) {
                $name = array_key_first($param);
                $type = $param[$name];

                return "{$type} \${$name}";
            }, $method['parameters']);

            $signature = implode(', ', $parameters);
            $returnType = $method['return'] ? ": {$method['return']}" : '';
            $methods[] = "    public function {$method['name']}({$signature}){$returnType};";
        }

        return implode("\n", $methods);
    }

    /**
     * Generate the method stubs for the repository based on the interface configuration.
     *
     * @return string
     */
    protected function generateRepositoryMethods (): string
    {
        $methodsConfig = config('repository.interfaces', []);
        $methods = [];

        foreach ($methodsConfig as $method) {
            $parameters = array_map(function ($param) {
                $name = array_key_first($param);
                $type = $param[$name];

                return "{$type} \${$name}";
            }, $method['parameters']);

            $signature = implode(', ', $parameters);
            $returnType = $method['return'] ? ": {$method['return']}" : '';
            $logic = $method['logic'] ?? "// TODO: Implement {$method['name']}() method.";

            $methods[] = <<<METHOD
    public function {$method['name']}({$signature}){$returnType}
    {
        {$logic}
    }
METHOD;
        }

        return implode("\n\n", $methods);
    }

}
