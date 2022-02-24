
# RepoService Generator

RepoService Generator is a Laravel package that generates service and interface files for [Repository-Service Pattern](https://www.vodovnik.com/2015/08/26/repository-and-services-pattern-in-a-multilayered-architecture/).

- The repository is a layer between the domain and data layers of your application to perform CRUD operations.
- A service applies the business logic of your application. It simply performs the a set task using the information provided, using any repositories or other classes you have created outside of the service.

## Installation

You can install the package via composer:

```bash
composer require tgalfa/reposervice --dev
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="reposervice-config"
```

You can set the Repository and Service folders path and namespaces in the config file:

```php
return [
    ...
    'namespaces' => [
        ....
        'repositories' => 'App\Repositories',
        'repositoryContracts' => 'App\Repositories\Contracts',

        'services' => 'App\Services',
        'serviceContracts' => 'App\Services\Contracts',
        ....
    ],
    ....
    'paths' => [
        'repositories' => 'app/Repositories/',
        'repositoryContracts' => 'app/Repositories/Contracts/',

        'services' => 'app/Services/',
        'serviceContracts' => 'app/Services/Contracts/',
    ],
    ....
];
```
All generated Services and Repositories extend an abstract class.
If you do not want to use the default AbstractMainService and AbstractMainRepository classes you can create your own and use those instead. Update your `reposervice` config:

```php
return [
    ...
    'namespaces' => [
        ....
        'main' => [
            'repository' => 'App\Repositories',
            'repositoryContract' => 'App\Repositories\Contracts',

            'service' => 'App\Services',
            'serviceContract' => 'App\Services\Contracts',
        ],
    ],
    ....
    'main' => [
        'repository' => 'MyAbstractMainRepository',
        'repositoryContract' => 'MyMainRepositoryInterface',

        'service' => 'MyAbstractMainService',
        'serviceContract' => 'MyMainServiceInterface',
    ],
];
```

## Usage

Before using the generate commdand you should customize `config/reposervice.php` for your own use.
You can simply use `reposervice:generate` and pass your model class as a parameter:

``` bash
php artisan reposervice:generate Post
```

## Available Methods

The following methods are available:

##### tgalfa\RepoService\Services\Contracts\MainServiceInterface

```php
    public function getModel();

    public function get(array $columns = ['*'], array $scopes = []]);

    public function paginate(
        int $perPage,
        array $columns = ['*'],
        array $scopes = []
    );

    public function getById(int $id, array $columns = ['*']);

    public function store(array $data, array $scopes = []);

    public function update(Model $model, array $data, array $scopes = []);

    public function delete(Model $model);
```

### Example usage

Get Active Posts with the category_id = 5:
```php
$post = $this->postService->get(
    ['id', 'title', 'description'],
    ['isActive', 'byCategory' => 5]
);
```
You need to create the relevent scopes in your Model class to use them. Example:
```php
public function scopeIsActive(Builder $query)
{
    return $query->where('active', 1);
}
public function scopeByCategory(Builder $query, int $categoryId)
{
    return $query->where('category_id', $categoryId);
}
```

Get Paginated Posts:
```php
$post = $this->postService->paginate(
    8,
    ['id', 'title', 'description'],
);
```

Create a new Post in repository:
```php
$post = $this->postService->store($request->all());
```

Update existing Post:
```php
$post = $this->postService->update($post, $request->all());
```

Delete Post:
```php
$post = $this->postService->delete($post);
```

##### tgalfa\RepoService\Services\Contracts\MainRepositoryInterface
```php
    public function getModel();

    public function get(
        array $columns = ['*'],
        array $scopes = [],
        int $perPage = null
    );

    public function paginate(
        int $perPage,
        array $columns = ['*'],
        array $scopes = []
    );

    public function getById(int $id, array $columns = ['*']);

    public function store(array $data);

    public function update(Model $model, array $data);

    public function delete(Model $model);
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
