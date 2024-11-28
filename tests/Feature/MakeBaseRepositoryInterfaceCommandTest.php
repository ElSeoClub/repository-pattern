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
        $filesystem = new Filesystem();

        $this->assertTrue(is_dir(base_path('app')), 'The app directory does not exist.');

        $command = new MakeBaseRepositoryInterfaceCommand($filesystem);

        $command->setLaravel($this->app);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);

        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        $this->assertTrue($filesystem->exists($targetPath), 'The base repository interface was not created.');
    }
}
