<?php

namespace tgalfa\RepoService;

class RepoService
{
    /**
     * Check if config has been published.
     *
     * @return bool
     */
    public static function configNotPublished(): bool
    {
        return is_null(config('reposervice'));
    }
}
