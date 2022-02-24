<?php

namespace tgalfa\RepoService\Tests\Service;

use tgalfa\RepoService\Services\AbstractMainService;
use tgalfa\RepoService\Tests\Repository\TestRepository;

class TestService extends AbstractMainService
{
    /**
     * @return string
     */
    public function repository()
    {
        return TestRepository::class;
    }
}
