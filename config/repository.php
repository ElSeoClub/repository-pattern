<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model Namespace
    |--------------------------------------------------------------------------
    |
    | Here you can define the base namespace for the models in your application.
    | This will be used by the generation command to locate the models.
    | By default, it is set to 'App\Models'.
    |
    */
    'model_namespace' => 'App\\Models',

    /*
    |--------------------------------------------------------------------------
    | Model Base Directory
    |--------------------------------------------------------------------------
    |
    | Here you can define the base directory for your models. This directory
    | will be used to search for model files. By default, it points to
    | 'app/Models', but you can customize it as needed.
    |
    */
    'base_dir' => 'app/Models',

    /*
    |--------------------------------------------------------------------------
    | Cache Enabled
    |--------------------------------------------------------------------------
    |
    | Important: When this value is changed, make sure to run:
    |
    | php artisan repository:with-cache true
    |
    | or
    |
    | php artisan repository:with-cache false
    |
    | To update the container bindings.
    |
    */
    'cache' => false,

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
            'name' => 'findById',
            'parameters' => [
                ['id' => 'int'],
            ],
            'return' => '?{{model}}',
            'logic' => 'return $this->model->find($id);',
        ],
        [
            'name' => 'findAll',
            'parameters' => [],
            'return' => 'Collection',
            'logic' => 'return $this->model->all();',
        ],
        [
            'name' => 'create',
            'parameters' => [
                ['data' => 'array'],
            ],
            'return' => '{{model}}',
            'logic' => 'return $this->model->create($data);',
        ],

        [
            'name' => 'update',
            'parameters' => [
                ['id' => 'int'],
                ['data' => 'array'],
            ],
            'return' => 'bool',
            'logic' => 'return $this->model->where(\'id\', $id)->update($data);',
        ],
    ],
];

