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

        $this->app->instance('path', base_path());
    }

    /** @test */
    public function it_creates_base_repository_interface ()
    {
        $filesystem = new Filesystem();

        $this->assertTrue(is_dir(base_path('app')), 'La ruta base del entorno de Laravel no es correcta.');

        $command = new MakeBaseRepositoryInterfaceCommand($filesystem);
        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->setLaravel($this->app);
        $command->run($input, $output);

        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        $this->assertTrue($filesystem->exists($targetPath), 'El archivo BaseRepositoryInterface no fue creado correctamente.');
    }

    protected function getPackageProviders ($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp ($app)
    {
    }
}
