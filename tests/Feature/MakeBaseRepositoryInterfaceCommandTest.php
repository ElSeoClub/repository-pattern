<?php

namespace Tests\Feature;

use Elseoclub\RepositoryPattern\Console\Commands\MakeBaseRepositoryInterfaceCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeBaseRepositoryInterfaceCommandTest extends TestCase
{
    protected function setUp (): void
    {
        parent::setUp();

        // Configuración necesaria para la ruta de los stubs
        $this->app->instance('path', base_path()); // Ajustar la ruta a src
    }

    /** @test */
    public function it_creates_base_repository_interface ()
    {
        // Preparar
        $stubPath = __DIR__ . '/../../src/stubs/BaseRepositoryInterface.stub';
        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');

        $targetDirectory = base_path('app/Repositories/Interfaces');
        $targetPath = $targetDirectory . '/BaseRepositoryInterface.php';

        $filesystem = new Filesystem();

        // Crear el directorio si no existe
        if (!$filesystem->exists($targetDirectory)) {
            $filesystem->makeDirectory($targetDirectory, 0755, true);
        }

        // Limpiar archivo existente si está presente
        if ($filesystem->exists($targetPath)) {
            $filesystem->delete($targetPath);
        }

        $command = new MakeBaseRepositoryInterfaceCommand($filesystem);
        $input = new ArrayInput([]);
        $output = new NullOutput();

        // Ejecutar el comando
        $command->setLaravel($this->app);
        $command->run($input, $output);

        // Comprobaciones
        $this->assertTrue($filesystem->exists($targetPath), 'El archivo BaseRepositoryInterface no fue creado correctamente.');
        $this->assertFileEquals($stubPath, $targetPath);
    }

    protected function getPackageProviders ($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp ($app)
    {
    }
}
