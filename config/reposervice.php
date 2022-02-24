<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | The namespace of Contracts, Models, Repositories and Services.
    |
    */
    'namespaces' => [
        'models' => 'App\Models',

        'repositories' => 'App\Repositories',
        'repositoryContracts' => 'App\Repositories\Contracts',

        'services' => 'App\Services',
        'serviceContracts' => 'App\Services\Contracts',

        'main' => [
            'repository' => 'tgalfa\RepoService\Repositories',
            'repositoryContract' => 'tgalfa\RepoService\Repositories\Contracts',

            'service' => 'tgalfa\RepoService\Services',
            'serviceContract' => 'tgalfa\RepoService\Services\Contracts',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Directory Paths
    |--------------------------------------------------------------------------
    |
    | The default directory structure.
    |
    */
    'paths' => [
        'models' => 'app/Models/',

        'repositories' => 'app/Repositories/',
        'repositoryContracts' => 'app/Repositories/Contracts/',

        'services' => 'app/Services/',
        'serviceContracts' => 'app/Services/Contracts/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Main Files
    |--------------------------------------------------------------------------
    |
    | The main Contract, Repository and Service class,
    | other classes will be extended from this.
    */
    'main' => [
        'repository' => 'AbstractMainRepository',
        'repositoryContract' => 'MainRepositoryInterface',

        'service' => 'AbstractMainService',
        'serviceContract' => 'MainServiceInterface',
    ],
];
