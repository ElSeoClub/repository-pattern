<?php

namespace Elseoclub\RepositoryPattern\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class RepositoryBindCommand extends Command
{
    protected $signature = 'repository:bind {type}';
    protected $description = 'Set the container binding to use the specified repository type (default or cache).';

    private Filesystem $filesystem;

    public function __construct (Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle (): void
    {
        $providerPath = base_path('app/Providers/ModelRepositoryServiceProvider.php');
        $type = $this->argument('type');

        if (!$this->filesystem->exists($providerPath)) {
            $this->warn('No repository provider file found at app/Providers/ModelRepositoryServiceProvider.php');

            return;
        }

        try {
            try {
                $content = $this->filesystem->get($providerPath);
            } catch (FileNotFoundException) {
                $this->error('The provider file could not be read.');

                exit(1);
            }

            if ($type === 'cache') {
                $updatedContent = str_replace('CacheRepository::class', 'Repository::class', $content);
                $updatedContent = str_replace('Repository::class', 'CacheRepository::class', $updatedContent);
            } elseif ($type === 'default') {
                $updatedContent = str_replace('CacheRepository::class', 'Repository::class', $content);
            } else {
                $this->error('Invalid type specified. Use "default" or "cache".');

                exit(1);
            }

            if ($updatedContent !== null && $updatedContent !== $content) {
                $this->filesystem->put($providerPath, $updatedContent);
                $this->info('Bindings updated to use ' . $type . ' repositories.');
            } else {
                $this->warn('No bindings were updated.');
            }
        } catch (Exception $e) {
            $this->error('The provider file could not be read or updated: ' . $e->getMessage());
        }
    }
}
