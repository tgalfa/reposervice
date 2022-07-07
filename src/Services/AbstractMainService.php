<?php

namespace tgalfa\RepoService\Services;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use tgalfa\RepoService\Repositories\AbstractMainRepository;
use tgalfa\RepoService\Services\Contracts\MainServiceInterface;

abstract class AbstractMainService implements MainServiceInterface
{
    /**
     * The Repository.
     *
     * @var \tgalfa\RepoService\Repositories\AbstractMainRepository|static
     */
    protected $repository;

    /**
     * Specify Repository class name.
     *
     * @return mixed
     */
    abstract public function repository();

    /**
     * AbstractMainService constructor.
     */
    public function __construct()
    {
        $this->makeRepository();
    }

    /**
     * Initialize Repository.
     *
     * @return tgalfa\RepoService\Repositories\AbstractMainRepository|mixed
     *
     * @throws Exception
     */
    public function makeRepository()
    {
        $repository = app()->make($this->repository());
        if (! $repository instanceof AbstractMainRepository) {
            throw new Exception(
                "Repository {$this->repository()} must be an instance of ".
                AbstractMainRepository::class
            );
        }

        return $this->repository = $repository;
    }

    /**
     * Get Model.
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function getModel()
    {
        return $this->repository->getModel();
    }

    /**
     * Get all the Model records in the database.
     * Apply scopes with the {$scopes} parameter.
     * Example:
     * ['myscope', 'myscopeWithParam' => 'myscopeParam'].
     *
     * @param  array  $columns    List of selected columns
     * @param  array  $scopes     Array with scope names and its parameters
     * @param  int|null  $limit   Number of items would be displayed
     * @return \Illuminate\Support\Collection
     */
    public function get(
        array $columns = ['*'],
        array $scopes = [],
        int $limit = null
    ): Collection {
        return $this->repository->get($columns, $scopes, $limit);
    }

    /**
     * Get paginated Model records in the database.
     * Apply scopes with the {$scopes} parameter.
     * Example:
     * ['myscope', 'myscopeWithParam' => 'myscopeParam'].
     *
     * @param  int  $perPage    Number of items would be displayed
     * @param  array  $columns  List of selected columns
     * @param  array  $scopes   Array with scope names and its parameters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(
        int $perPage,
        array $columns = ['*'],
        array $scopes = []
    ): LengthAwarePaginator {
        return $this->repository->paginate($perPage, $columns, $scopes);
    }

    /**
     * Get Model by id.
     *
     * @param  int  $id         Id of searched Model
     * @param  array  $columns  List of selected columns
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function getById(int $id, array $columns = ['*'])
    {
        return $this->repository->getById($id, $columns);
    }

    /**
     * Save Model data.
     *
     * @param  array  $data    Data to be stored
     * @param  array  $scopes  Array with scope names and its parameters
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function store(array $data, array $scopes = [])
    {
        $model = $this->repository->store($data);

        if (! empty($scopes)) {
            $model = $this->loadScopes($model, $scopes);
        }

        return $model;
    }

    /**
     * Update Model data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data    Data to be stored
     * @param  array  $scopes  Array with scope names and its parameters
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function update(Model $model, array $data, array $scopes = [])
    {
        $model = $this->repository->update($model, $data);

        if (! empty($scopes)) {
            $model = $this->loadScopes($model, $scopes);
        }

        return $model;
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     * Store to DB if there are no errors.
     *
     * @param  array  $attributes
     * @param  array  $data
     * @param  array  $scopes  Array with scope names and its parameters
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function updateOrCreate(array $attributes, array $data, array $scopes = [])
    {
        $model = $this->repository->updateOrCreate($attributes, $data);

        if (! empty($scopes)) {
            $model = $this->loadScopes($model, $scopes);
        }

        return $model;
    }

    /**
     * Delete Model by id.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function delete(Model $model): void
    {
        $this->repository->delete($model);
    }

    /**
     * Eager load relationships of the Model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $scopes  Array with scope names and its parameters
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    private function loadScopes(Model $model, array $scopes)
    {
        foreach ($scopes as $key => $value) {
            $hasValue = is_string($key);
            $model->{$hasValue ? $key : $value}($hasValue ? $value : null);
        }

        return $model;
    }
}
