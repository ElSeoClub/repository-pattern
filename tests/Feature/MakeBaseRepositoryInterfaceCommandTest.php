<?php

namespace Tests\Feature;

use Elseoclub\RepositoryPattern\Console\Commands\MakeBaseRepositoryInterfaceCommand;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeBaseRepositoryInterfaceCommandTest extends TestCase
{
    protected function setUp (): void
    {
        parent::setUp();

        $filesystem = new Filesystem();
        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        if ($filesystem->exists($targetPath)) {
            $filesystem->delete($targetPath);
        }
    }

    /** @test */
    public function it_creates_base_repository_interface ()
    {
        // Crea una instancia del Filesystem para interactuar con el sistema de archivos
        $filesystem = new Filesystem();

        // Verifica que el directorio base exista
        $this->assertTrue(is_dir(base_path('app')), 'La ruta base del entorno de Laravel no es correcta.');

        // Crea una instancia del comando a probar
        $command = new MakeBaseRepositoryInterfaceCommand($filesystem);

        // Establece la aplicación de Laravel en el comando
        $command->setLaravel($this->app);

        // Prepara la entrada y salida vacías para el comando
        $input = new ArrayInput([]);
        $output = new NullOutput();

        // Ejecuta el comando
        $command->run($input, $output);

        // Verifica si el archivo fue creado correctamente
        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        $this->assertTrue($filesystem->exists($targetPath), 'El archivo BaseRepositoryInterface no fue creado correctamente.');
    }
}
