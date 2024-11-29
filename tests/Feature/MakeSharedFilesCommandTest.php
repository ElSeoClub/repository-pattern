<?php

namespace Feature;

use Elseoclub\RepositoryPattern\Console\Commands\Shared\MakeSharedFilesCommand;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MakeSharedFilesCommandTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
    }

    #[Test]
    public function it_creates_base_repository_interface()
    {
        $this->executeCommand();
        $targetPath = base_path('app/Core/Shared/Sanitization/SanitizationProcessor.php');

        $this->assertTrue($this->filesystem->exists($targetPath), 'The base repository interface was not created.');
    }

    private function executeCommand(): void
    {
        $this->filesystem = new Filesystem();
        $command          = new MakeSharedFilesCommand($this->filesystem);
        $command->setLaravel($this->app);

        $input  = new ArrayInput([]);
        $output = new NullOutput();

        $command->run($input, $output);
    }
}
