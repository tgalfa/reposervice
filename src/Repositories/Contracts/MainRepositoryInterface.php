<?php

namespace tgalfa\RepoService\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface MainRepositoryInterface
{
    public function makeModel();

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
}
