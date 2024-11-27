<?php

namespace Elseoclub\RepositoryPattern\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Artisan;
use Elseoclub\RepositoryPattern\Providers\ModelRepositoryServiceProvider;
use Illuminate\Filesystem\Filesystem;

class MakeRepositoryCommandTest extends TestCase
{
    protected function getPackageProviders ($app)
    {
        return [
            ModelRepositoryServiceProvider::class,
        ];
    }

    /** @test */
    public function it_creates_repository_and_interface_files ()
    {
        $modelPath = app_path('Models/TestModel.php');
        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists(dirname($modelPath));
        $filesystem->put($modelPath, '<?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;

        class TestModel extends Model
        {
            // Test model
        }');

        $model = 'TestModel';

        // Run the command
        Artisan::call('make:repository', ['model' => $model]);

        // Define paths for the generated files
        $repositoryPath = app_path("Repositories/{$model}Repository.php");
        $interfacePath = app_path("Repositories/Interfaces/{$model}Interface.php");
        $providerPath = app_path('Providers/ModelRepositoryServiceProvider.php');

        // Assert that the files have been created
        $this->assertFileExists($repositoryPath);
        $this->assertFileExists($interfacePath);
        $this->assertFileExists($providerPath);

        // Clean up the generated files
        $filesystem = new Filesystem();
        $filesystem->delete([$repositoryPath, $interfacePath, $providerPath, $modelPath]);
    }

    /** @test */
    public function it_updates_service_provider_bindings ()
    {
        $modelPath = app_path('Models/TestModel.php');
        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists(dirname($modelPath));
        $filesystem->put($modelPath, '<?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;

        class TestModel extends Model
        {
            // Test model
        }');

        $model = 'TestModel';

        // Run the command
        Artisan::call('make:repository', ['model' => $model]);

        // Define the path for the generated provider file
        $providerPath = app_path('Providers/ModelRepositoryServiceProvider.php');

        // Assert that the service provider file has been updated with the correct binding
        $this->assertFileExists($providerPath);
        $content = file_get_contents($providerPath);
        $expectedBinding = "\$this->app->bind(\\App\\Repositories\\Interfaces\\{$model}Interface::class, \\App\\Repositories\\{$model}Repository::class);";
        $this->assertStringContainsString($expectedBinding, $content);

        // Clean up the generated file
        $filesystem = new Filesystem();
        $filesystem->delete($providerPath);

    }

    /** @test */
    public function it_fails_when_model_does_not_exist ()
    {
        $model = 'NonExistentModel';

        // Run the command
        $exitCode = Artisan::call('make:repository', ['model' => $model]);

        // Assert that the command fails
        $this->assertNotEquals(0, $exitCode);

        // Define paths for the generated files
        $repositoryPath = app_path("Repositories/{$model}Repository.php");
        $interfacePath = app_path("Repositories/Interfaces/{$model}Interface.php");
        $providerPath = app_path('Providers/ModelRepositoryServiceProvider.php');

        // Assert that the files have NOT been created
        $this->assertFileDoesNotExist($repositoryPath);
        $this->assertFileDoesNotExist($interfacePath);
        $this->assertFileDoesNotExist($providerPath);
    }
}
