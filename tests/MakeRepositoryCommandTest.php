<?php

namespace Elseoclub\RepositoryPattern\Tests;

use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use Illuminate\Filesystem\Filesystem;
use Elseoclub\RepositoryPattern\Providers\ModelRepositoryServiceProvider;

class MakeRepositoryCommandTest extends TestCase
{
    protected function getPackageProviders ($app)
    {
        return [
            ModelRepositoryServiceProvider::class,
        ];
    }

    protected function setUp (): void
    {
        parent::setUp();

        // Crear un archivo de modelo de prueba
        $filesystem = new Filesystem();
        if (!$filesystem->exists(base_path('app/Models/Nested'))) {
            $filesystem->makeDirectory(base_path('app/Models/Nested'), 0755, true);
            echo 'Created directory: ' . base_path('app/Models/Nested') . "\n";
        }

        $filesystem->put(base_path('app/Models/TestModel.php'), "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass TestModel extends Model\n{\n    // Test Model\n}");
        require_once base_path('app/Models/TestModel.php');

        $filesystem->put(base_path('app/Models/Nested/NestedModel.php'), "<?php\n\nnamespace App\Models\Nested;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass NestedModel extends Model\n{\n    // Test Model\n}");
        require_once base_path('app/Models/Nested/NestedModel.php');
    }

    /** @test */
    public function it_creates_test_model ()
    {
        // Verificar que el archivo de modelo de prueba fue creado
        $modelPath = base_path('app/Models/TestModel.php');
        $this->assertFileExists($modelPath, 'The TestModel.php file was not created successfully.');
    }

    /** @test */
    public function it_creates_nested_model ()
    {
        // Verificar que el archivo de modelo de prueba fue creado
        $modelPath = base_path('app/Models/Nested/NestedModel.php');
        $this->assertFileExists($modelPath, 'The NestedModel.php file was not created successfully.');
    }

    /** @test */
    public function it_creates_repository_and_interface_files ()
    {
        $model = 'TestModel';

        // Run the command
        try {
            Artisan::call('make:repository', ['model' => $model]);
        } catch (\Exception $e) {
            $this->fail('Command failed: ' . $e->getMessage());
        }

        // Define paths for the generated files
        $repositoryPath = base_path("app/Repositories/{$model}Repository.php");
        $interfacePath = base_path("app/Repositories/Interfaces/{$model}Interface.php");
        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');

        // Assert that the files have been created
        $this->assertFileExists($repositoryPath, "Repository file was not created at {$repositoryPath}");
        $this->assertFileExists($interfacePath, "Interface file was not created at {$interfacePath}");
        $this->assertFileExists($providerPath, "Provider file was not created at {$providerPath}");

        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');

        // Assert that the provider file contains the correct binding
        $this->assertFileExists($providerPath, "Provider file was not created at {$providerPath}");
        $providerContent = file_get_contents($providerPath);
        $expectedBinding = "\$this->app->bind(\\App\\Repositories\\Interfaces\\{$model}Interface::class, \\App\\Repositories\\{$model}Repository::class);";
        $this->assertStringContainsString($expectedBinding, $providerContent, "The provider file does not contain the expected binding for {$model}.");
    }

    /** @test */
    public function it_creates_nested_repository_and_interface_files ()
    {
        $model = 'NestedModel';

        // Run the command
        try {
            Artisan::call('make:repository', ['model' => $model]);
        } catch (\Exception $e) {
            $this->fail('Command failed: ' . $e->getMessage());
        }

        // Define paths for the generated files
        $repositoryPath = base_path("app/Repositories/Nested/{$model}Repository.php");
        $interfacePath = base_path("app/Repositories/Interfaces/Nested/{$model}Interface.php");
        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');

        // Assert that the files have been created
        $this->assertFileExists($repositoryPath, "Repository file was not created at {$repositoryPath}");
        $this->assertFileExists($interfacePath, "Interface file was not created at {$interfacePath}");
        $this->assertFileExists($providerPath, "Provider file was not created at {$providerPath}");

        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');

        // Assert that the provider file contains the correct binding
        $this->assertFileExists($providerPath, "Provider file was not created at {$providerPath}");
        $providerContent = file_get_contents($providerPath);
        $expectedBinding = "\$this->app->bind(\\App\\Repositories\\Interfaces\\Nested\\{$model}Interface::class, \\App\\Repositories\\Nested\\{$model}Repository::class);";
        $this->assertStringContainsString($expectedBinding, $providerContent, "The provider file does not contain the expected binding for {$model}.");
        $expectedBinding = "\$this->app->bind(\\App\\Repositories\\Interfaces\\TestModelInterface::class, \\App\\Repositories\\TestModelRepository::class);";
        $this->assertStringContainsString($expectedBinding, $providerContent, "The provider file does not contain the expected binding for TestModel.");
    }

    /** @test */
    public function it_checks_generated_files_for_php_errors ()
    {
        $model = 'TestModel';
        $modelNested = 'NestedModel';

        // Define paths for the generated files
        $repositoryPath = base_path("app/Repositories/{$model}Repository.php");
        $interfacePath = base_path("app/Repositories/Interfaces/{$model}Interface.php");
        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');
        $repositoryNestedPath = base_path("app/Repositories/Nested/{$modelNested}Repository.php");
        $interfaceNestedPath = base_path("app/Repositories/Interfaces/Nested/{$modelNested}Interface.php");

        // Assert that the files have been created without PHP errors by requiring them
        try {
            require_once $interfacePath;
            require_once $repositoryPath;
            require_once $interfaceNestedPath;
            require_once $repositoryNestedPath;
            require_once $providerPath;
        } catch (\Throwable $e) {
            $this->fail('PHP error in generated files: ' . $e->getMessage());
        }

        $this->assertTrue(true, 'The generated files do not contain PHP errors.');
    }
}
