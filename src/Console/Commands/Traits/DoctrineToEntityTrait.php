<?php

namespace Elseoclub\RepositoryPattern\Console\Commands\Traits;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\Schema;

trait DoctrineToEntityTrait
{
    use FindModelTrait;

    protected function getDoctrinePlatform (): AbstractPlatform
    {
        $defaultConnection = config('database.default');

        $config = config('database.connections.' . $defaultConnection);

        $connectionParams = [
            'dbname' => $config['database'],
            'user' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'port' => $config['port'],
            'driver' => $this->mapLaravelDriverToDoctrine($config['driver']),
            'charset' => $config['charset'] ?? 'utf8mb4',
        ];

        $doctrineConnection = DriverManager::getConnection($connectionParams);

        return $doctrineConnection->getDatabasePlatform();
    }

    protected function getDoctrineSchemaManager (): AbstractSchemaManager
    {
        $defaultConnection = config('database.default');

        $config = config('database.connections.' . $defaultConnection);

        $connectionParams = [
            'dbname' => $config['database'],
            'user' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'port' => $config['port'],
            'driver' => $this->mapLaravelDriverToDoctrine($config['driver']),
            'charset' => $config['charset'] ?? 'utf8mb4',
        ];

        $doctrineConnection = DriverManager::getConnection($connectionParams);

        return $doctrineConnection->createSchemaManager();
    }

    protected function getTableColumnsWithDetails (): array
    {
        $schemaManager = $this->getDoctrineSchemaManager();
        $platform = $this->getDoctrinePlatform();
        $details = [];
        $columns = $schemaManager->listTableColumns($this->getTable());

        foreach ($columns as $column) {
            $details[$column->getName()] = [
                'type' => explode('(', explode(' ', $column->getType()->getSQLDeclaration($column->toArray(), $platform))[0])[0],
                'length' => $column->getLength(),
                'precision' => $column->getPrecision(),
                'scale' => $column->getScale(),
                'unsigned' => $column->getUnsigned(),
                'fixed' => $column->getFixed(),
                'notnull' => $column->getNotnull(),
                'default' => $column->getDefault(),
                'autoincrement' => $column->getAutoincrement(),
                'comment' => $column->getComment(),
            ];
        }

        return $details;
    }

    protected function mapLaravelDriverToDoctrine (string $driver): string
    {
        $map = [
            'mysql' => 'pdo_mysql',
            'pgsql' => 'pdo_pgsql',
            'sqlite' => 'pdo_sqlite',
            'sqlsrv' => 'pdo_sqlsrv',
        ];

        return $map[$driver] ?? $driver;
    }

    protected function getTableColumns (): array
    {
        return Schema::getColumnListing($this->getTable());
    }

    protected function getTable (): string
    {
        return (new $this->modelClass)->getTable();
    }
}
