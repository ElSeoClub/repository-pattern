<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Use cache repository
    |--------------------------------------------------------------------------
    |
    | Important: When this value is changed, make sure to run:
    |
    | php artisan repository:bind-cache
    |
    | or
    |
    | php artisan repository:bind-default
    |
    */
    'cache'      => false,

    /*
    |--------------------------------------------------------------------------
    | Default Interface Methods
    |--------------------------------------------------------------------------
    |
    | Define the methods that should automatically be added to repository
    | interfaces. Each method includes a name, input parameters, and a
    | return type. Parameters should be defined as an array of [name => type].
    |
    */
    'interfaces' => [
        [
            'name'       => 'find',
            'parameters' => [
                ['id' => 'int'],
            ],
            'return'     => '?DummyModel',
            'logic'      => [
                'default' => 'return $this->model->find($id);',
                'cache'   => 'return Cache::remember("DummyModel:{$id}", 60, function() use ($id) { return $this->repository->find($id); });',
            ],
        ],
        [
            'name'       => 'all',
            'parameters' => [],
            'return'     => 'Collection',
            'logic'      => [
                'default' => 'return $this->model->all();',
                'cache'   => 'return Cache::remember("DummyModel:all", 60, function() { return $this->repository->all(); });',
            ],
        ],
        [
            'name'       => 'create',
            'parameters' => [
                ['data' => 'array'],
            ],
            'return'     => 'DummyModel',
            'logic'      => [
                'default' => 'return $this->model->create($data);',
                'cache'   => '
                    $created = $this->repository->create($data);
                    Cache::forget("DummyModel:all");
                    Cache::put("DummyModel:{$created->id}", $created, 60);
                    return $created;
                ',
            ],
        ],
        [
            'name'       => 'update',
            'parameters' => [
                ['id' => 'int'],
                ['data' => 'array'],
            ],
            'return'     => 'bool',
            'logic'      => [
                'default' => 'return $this->model->where(\'id\', $id)->update($data);',
                'cache'   => '
                    $updated = $this->repository->update($id, $data);
                    if ($updated) {
                        Cache::forget("DummyModel:{$id}");
                        Cache::forget("DummyModel:all");
                        Cache::put("DummyModel:{$id}", $this->repository->find($id), 60);
                    }
                    return $updated;
                ',
            ],
        ],
        [
            'name'       => 'delete',
            'parameters' => [
                ['id' => 'int'],
            ],
            'return'     => 'bool',
            'logic'      => [
                'default' => 'return $this->model->where(\'id\', $id)->delete();',
                'cache'   => '
                    $deleted = $this->repository->delete($id);
                    if ($deleted) {
                        Cache::forget("DummyModel:{$id}");
                        Cache::forget("DummyModel:all");
                    }
                    return $deleted;
                ',
            ],
        ],
    ],

    'ValueObjectExtends' => [
        'id'    => [
            ['BIGINT', \App\Core\Shared\ValueObjects\IdValueObject::class],
            ['CHAR', \App\Core\Shared\ValueObjects\UuidValueObject::class],
        ],
        'email' => \App\Core\Shared\ValueObjects\EmailValueObject::class,
    ],
];

