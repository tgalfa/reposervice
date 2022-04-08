<?php

namespace tgalfa\RepoService\Services\Contracts;

use Illuminate\Database\Eloquent\Model;

interface MainServiceInterface
{
    public function makeRepository();

    public function getModel();

    public function get(array $columns = ['*'], array $scopes = []);

    public function paginate(
        int $perPage,
        array $columns = ['*'],
        array $scopes = []
    );

    public function getById(int $id, array $columns = ['*']);

    public function store(array $data, array $scopes = []);

    public function update(Model $model, array $data, array $scopes = []);

    public function updateOrCreate(array $attributes, array $data, array $scopes = []);

    public function delete(Model $model);
}
