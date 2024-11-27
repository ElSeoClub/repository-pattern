# Laravel Repository Pattern Package

This package allows you to easily generate repository classes, interfaces, and service providers for your Laravel models. By following a consistent repository pattern, it provides a clear separation of concerns for managing data access logic.

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

## Configuration Options

The configuration file (`config/repository.php`) contains the following options:

- **Cache Repository**: When `cache` is set to `true`, the package will use `YourModelCacheRepository` instead of `YourModelRepository` to provide caching functionality. This can help improve performance by reducing the need for repetitive database queries.

- **Model Namespace**: Define the base namespace for your models (default: `App\Models`).

- **Model Base Directory**: Specify the base directory for your models (default: `app/Models`).

- **Default Interface Methods**: Define the default methods for generated repository interfaces, including their parameters, return types, and logic. Methods like `findById`, `findAll`, `create`, and `update` are included by default, and you can modify or add more as needed.

## Cache Repository

When caching is enabled in the configuration (`'cache' => true`), the generated repository will use a `YourModelCacheRepository` instead of `YourModelRepository`. This repository is designed to provide caching for common queries, which can significantly improve performance, especially for frequently accessed data. To switch between using the regular repository and the cache-enabled repository, update the `cache` setting in `config/repository.php` and run the following command. This command will automatically update all bindings in the provider, allowing you to easily switch between direct queries and cache usage throughout your application:

```bash
php artisan repository:with-cache true
# or
php artisan repository:with-cache false
```

## Summary

This package helps you follow the repository pattern easily in Laravel, promoting better separation of concerns and making data access logic more maintainable. Feel free to customize the generated files and adjust the settings in the configuration file to fit your application's needs.

