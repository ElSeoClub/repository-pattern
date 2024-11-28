<?php

namespace Tests\Feature;

use Elseoclub\RepositoryPattern\Console\Commands\MakeBaseRepositoryInterfaceCommand;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeBaseRepositoryInterfaceCommandTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp (): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        if ($this->filesystem->exists($targetPath)) {
            $this->filesystem->delete($targetPath);
        }
    }

    /** @test */
    public function it_creates_base_repository_interface ()
    {

        $this->assertTrue(is_dir(base_path('app')), 'The app directory does not exist.');

        $this->executeCommand();

        $targetPath = base_path('app/Repositories/Interfaces/BaseRepositoryInterface.php');
        $this->assertTrue($this->filesystem->exists($targetPath), 'The base repository interface was not created.');
    }

    private function executeCommand (): void
    {
        $this->filesystem = new Filesystem();
        $command = new MakeBaseRepositoryInterfaceCommand($this->filesystem);
        $command->setLaravel($this->app);

        $input = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);
    }
}
