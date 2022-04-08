<?php

namespace tgalfa\RepoService\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use tgalfa\RepoService\Repositories\Contracts\MainRepositoryInterface;

abstract class AbstractMainRepository implements MainRepositoryInterface
{
    /**
     * The Repository Model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * MainRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * Initialize Model.
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed
     *
     * @throws Exception
     */
    public function makeModel()
    {
        $model = app()->make($this->model());
        if (! $model instanceof Model) {
            throw new Exception(
                "Class {$this->model()} must be an instance of ".
                Model::class
            );
        }

        return $this->model = $model;
    }

    /**
     * Get Model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get all the Model records in the database.
     * Apply scope with the {$scopes} parameter.
     * Example:
     * ['myscope', 'myscopeWithParam' => 'myscopeParam'].
     *
     * @param  array  $columns      List of selected columns
     * @param  array  $scopes       Array with scope names and its parameters
     * @param  int|null  $perPage   Number of items would be displayed
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(
        array $columns = ['*'],
        array $scopes = [],
        int $perPage = null
    ): Collection|LengthAwarePaginator {
        $query = $this->model->select($columns);

        // Apply scopes.
        if (! empty($scopes)) {
            foreach ($scopes as $scopeName => $param) {
                $scopeName = is_numeric($scopeName) && is_string($param)
                    ? $param
                    : $scopeName;

                if (is_string($scopeName)) {
                    $query->{$scopeName}($scopeName !== $param ? $param : null);
                }
            }
        }

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get paginated Model records in the database.
     * Apply scope with the {$scopes} parameter.
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
        return $this->get($columns, $scopes, $perPage);
    }

    /**
     * Get the specified Model record from the database.
     *
     * @param  int  $id         Id of searched Model
     * @param  array  $columns  List of selected columns
     * @return \Illuminate\Database\Eloquent\Model | null
     */
    public function getById(int $id, array $columns = ['*']): Model|null
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Create a new Model record in the database.
     * Store to DB if there are no errors.
     *
     * @param  array  $data  Data to be stored
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws InvalidArgumentException
     */
    public function store(array $data): Model
    {
        DB::beginTransaction();

        try {
            $model = new $this->model;
            $model->fill($this->trimData($data));
            $model->save();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info(json_encode($data));
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to save model data');
        }

        DB::commit();

        return $model->fresh();
    }

    /**
     * Update a Model record in the database.
     * Store to DB if there are no errors.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model  The Model
     * @param  array  $data                                 Data to be stored
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws InvalidArgumentException
     */
    public function update(Model $model, array $data): Model
    {
        $throwsException = false;
        $exception = 'Unable to update model data';

        DB::beginTransaction();

        try {
            if ($model->exists) {
                $model->update($this->trimData($data));
            } else {
                $throwsException = true;
            }
        } catch (Exception $e) {
            $throwsException = true;
            $exceptionMessage = $e->getMessage();
        }

        if ($throwsException) {
            DB::rollBack();
            Log::info($exceptionMessage ?? $exception);

            throw new InvalidArgumentException($exception);
        }

        DB::commit();

        return $model;
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     * Store to DB if there are no errors.
     *
     * @param  array  $attributes
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws InvalidArgumentException
     */
    public function updateOrCreate(array $attributes, array $data): Model
    {
        DB::beginTransaction();

        try {
            $model = $this->model->updateOrCreate($attributes, $this->trimData($data));
        } catch (Exception $e) {
            DB::rollBack();
            Log::info(json_encode($data));
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to update or save model data');
        }

        DB::commit();

        return $model;
    }

    /**
     * Delete a Model record from the database.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model  The Model
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function delete(Model $model): void
    {
        $throwsException = false;
        $exception = 'Unable to delete model data';

        DB::beginTransaction();

        try {
            if ($model->exists) {
                $model->delete();
            } else {
                $throwsException = true;
            }
        } catch (Exception $e) {
            $throwsException = true;
            $exceptionMessage = $e->getMessage();
        }

        if ($throwsException) {
            DB::rollBack();
            Log::info($exceptionMessage ?? $exception);

            throw new InvalidArgumentException($exception);
        }

        DB::commit();
    }

    /**
     * Trim whitspace from string Data.
     *
     * @param  string|array  $data
     * @return string|array
     */
    private function trimData(string|array $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->trimData($value);
            }
        } elseif (is_string($data)) {
            return trim($data);
        }

        return $data;
    }
}
