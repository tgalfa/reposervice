<?php

namespace tgalfa\RepoService\Tests\Repository;

use tgalfa\RepoService\Repositories\AbstractMainRepository;
use tgalfa\RepoService\Tests\Models\TestModel;

class TestRepository extends AbstractMainRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TestModel::class;
    }
}
