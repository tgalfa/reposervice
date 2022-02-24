<?php

namespace tgalfa\RepoService\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel(): Model
    {
        return $this->repository->getModel();
    }

    /**
     * Get all the Model records in the database.
     * Apply scopes with the {$scopes} parameter.
     * Example:
     * ['myscope', 'myscopeWithParam' => 'myscopeParam'].
     *
     * @param  array  $columns  List of selected columns
     * @param  array  $scopes   Array with scope names and its parameters
     * @return \Illuminate\Support\Collection
     */
    public function get(
        array $columns = ['*'],
        array $scopes = []
    ): Collection {
        return $this->repository->get($columns, $scopes);
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
     * @return \Illuminate\Pagination\LengthAwarePaginator
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getById(int $id, array $columns = ['*']): Model
    {
        return $this->repository->getById($id, $columns);
    }

    /**
     * Save Model data.
     *
     * @param  array  $data    Data to be stored
     * @param  array  $scopes  Array with scope names and its parameters
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $data, array $scopes = []): Model
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Model $model, array $data, array $scopes = []): Model
    {
        $model = $this->repository->update($model, $data);

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
     * @return \Illuminate\Database\Eloquent\Model
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
