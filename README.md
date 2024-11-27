# Laravel Repository Pattern Package

This package implements the repository pattern in Laravel, providing a **simple way to create repositories** for your
models. The repository pattern helps you to separate data access logic, and improve the maintainability of your
application, and the most importart **reduce the boilerplate code**.

## Requirements

- PHP 8.2 or higher
- Laravel 9 or higher

## Installation

To use the package, first install it in your Laravel project via Composer:

```bash
composer require elseoclub/laravel-repository-pattern
```

After installation, register the service provider if needed.

## Usage

### Generate a Repository

To generate a repository for your model, use the command:

```bash
php artisan make:repository YourModel
```

This command generates:

- A repository class at: `app/Repositories/YourModelRepository`
- An interface at: `app/Repositories/Interfaces/YourModelInterface`
- A service provider (`ModelRepositoryServiceProvider`) and automatically adds the binding:

```php
$this->app->bind(\App\Repositories\Interfaces\YourModelInterface::class, \App\Repositories\YourModelRepository::class);
```

### Example Usage in a Controller

To use the generated repository in a controller, inject the interface into your controller's constructor:

```php
namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserInterface;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->findAll();
        return view('users.index', compact('users'));
    }
}
```

### Publish Configuration

You can publish the configuration file to customize the package settings:

```bash
php artisan vendor:publish --tag=repository-config
```

This will create a `config/repository.php` file where you can adjust settings.

## Cache Repository

When cache is enabled in the config/repository.php `'cache' => true`, the new repositories created after this change
will use a `YourModelCacheRepository` instead of `YourModelRepository`. This repository is designed to provide caching
for common queries, which can significantly improve performance, especially for frequently accessed data.

To switch between using the regular repository and the cache-enabled repository, you can use the following commands:

```bash
php artisan repository:bind default
```

```bash
php artisan repository:bind cache
```

After running the command, all the existing repositories will be updated to use the specified repository type.

However, new repositorioes created with `php artisan make:repository YourModel` will use the specified repository type
in config.

## Manual Binding

If you prefer to manually bind the repositories, you can edit the `ModelRepositoryServiceProvider` and set the bindings
manually. This can be useful if you want to customize the repository bindings or use a different naming convention.

```php
$this->app->bind(\App\Repositories\Interfaces\UserInterface::class, \App\Repositories\UserRepository::class);
$this->app->bind(\App\Repositories\Interfaces\UserInterface::class, \App\Repositories\RoleCacheRepository::class);
```

## Custom Repository Methods

You can generate automatically the methods for the repository by editing the `config/repository.php`

```php
    
    'interfaces' => []

```

To do it you have to add an array with the name of the method and the parameters that it will receive.

```php
    'interfaces' => [
        [
            'name' => 'find', // Method name
            'parameters' => [
                ['id' => 'int'], // Parameter required in the method and the type
            ],
            'return' => '?{{model}}', // Return type of the method
            'logic' => [
                // The body of the method for default repository
                'default' => 'return $this->model->find($id);', 
                // The body of the method for cache repository
                'cache' => 'return Cache::remember("{{model}}:{$id}", 60, function() use ($id) { return $this->repository->find($id); });',
            ],
        ],
    ]
```

With this, the package will generate the methods for the repository.

```php
    //In the interface adds the method
    public function find(int $id): ?User;
    //In the repository adds the method
    public function find(int $id): ?User
    {
        return $this->model->find($id);
    }
```

## Using the repository in your controller

```php
namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserInterface;

class YourController extends Controller
{
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->find();
        return view('users.index', compact('users'));
    }
}
```

## Summary

This package helps you follow the repository pattern easily in Laravel, promoting better separation of concerns and
making data access logic more maintainable. Feel free to customize the generated files and adjust the settings in the
configuration file to fit your application's needs.

