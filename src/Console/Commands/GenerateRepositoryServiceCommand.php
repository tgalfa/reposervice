<?php

namespace tgalfa\RepoService\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateRepositoryServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reposervice:generate {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository and Service.';

    /**
     * Stub paths.
     *
     * @var array
     */
    protected $stubs = [
        'contracts'  => [
            'repository' => __DIR__.'/../Stubs/RepositoryInterface.stub',
            'service' => __DIR__.'/../Stubs/ServiceInterface.stub',
        ],
        'repository' => __DIR__.'/../Stubs/Repository.stub',
        'service' => __DIR__.'/../Stubs/Service.stub',
    ];

    /**
     * Model with full namespace.
     *
     * @var string
     */
    protected $model;

    /**
     * Model class name.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Model namespace.
     *
     * @var string
     */
    protected $modelNamespace;

    /**
     * Base Path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * File manager.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fileManager;

    /**
     * Check if Feature Test is running.
     *
     * @var bool
     */
    protected $isTesting;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileManager = app('files');
        $this->isTesting = strpos(app()->basePath(), 'testbench-core/laravel');
    }

    /**
     * Gets a configuration from package config file.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function config($key)
    {
        return config('reposervice.'.$key);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Check the models existance.
        $hasModel = $this->checkModel();

        // Return Error message if the Model doesn't exist in the filesystem.
        if (! $hasModel) {
            $this->error("Model [{$this->model}] doesn't exist. Please create it!");

            return false;
        }

        // Base path.
        $this->basePath = $this->isTesting
            ? dirname(__FILE__, 4).'/tests'
            : app()->basePath();

        // Create Repository Interface.
        [$repositoryContract, $repositoryContractName] = $this->createContract();
        // Create Repository.
        [$repository, $repositoryName] = $this->createRepository(
            $repositoryContract,
            $repositoryContractName
        );

        // Create Service Interface.
        [$serviceContract, $serviceContractName] = $this->createContract(true);
        // Create Service.
        $this->createService(
            $serviceContract,
            $serviceContractName,
            $repositoryName
        );
    }

    /**
     * Create a new Contract.
     *
     * @param  bool|bool  $isService  Pass true if Service contract is needed
     * @return array
     */
    protected function createContract(bool $isService = false): array
    {
        $prefix = $isService ? 'service' : 'repository';
        $ucPrefix = ucfirst($prefix);

        // Replace Stub's Placeholders.
        $content = $this->fileManager->get($this->stubs['contracts'][$prefix]);
        $replacements = [
            "%namespaces.{$prefix}Interfaces%" =>  $this->config("namespaces.{$prefix}Contracts"),
            "%namespaces.main{$ucPrefix}Interface%" => $this->config("namespaces.main.{$prefix}Contract"),
            "%main{$ucPrefix}Interface%" => $this->config("main.{$prefix}Contract"),
            '%modelName%' => $this->modelName,
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Create Folder & File.
        $fileName = "{$this->modelName}{$ucPrefix}Interface";
        $fileDirectory = $this->basePath.'/'.$this->config("paths.{$prefix}Contracts");
        $filePath = "{$fileDirectory}{$fileName}.php";

        // Check if the directory exists, if not create it.
        if (! $this->fileManager->exists($fileDirectory)) {
            $this->fileManager->makeDirectory($fileDirectory, 0755, true);
        }

        // Option to override if file exists.
        if ($this->laravel->runningInConsole() && $this->fileManager->exists($filePath)) {
            $response = $this->confirm("The contract [{$fileName}] already exists. Do you want to overwrite it?");

            if (! $response) {
                $this->line("The contract [{$fileName}] will not be overwritten.");
            } else {
                $this->fileManager->put($filePath, $content);
            }
        } else {
            $this->fileManager->put($filePath, $content);
        }

        $this->line("The contract [{$fileName}] has been created.");

        return [
            $this->config("namespaces.{$prefix}Contracts").'\\'.$fileName,
            $fileName,
        ];
    }

    /**
     * Create a new Repository.
     *
     * @param  string  $contract         Full path of the Interface
     * @param  string  $contractName     The name of the Interface
     * @return array
     */
    protected function createRepository(string $contract, string $contractName): array
    {
        // Replace Stub's Placeholders.
        $content = $this->fileManager->get($this->stubs['repository']);
        $replacements = [
            '%namespaces.repositories%' => $this->config('namespaces.repositories'),
            '%namespaces.repositoryInterface%' => $contract,
            '%repositoryInterface%' => $contractName,
            '%namespaces.model%' => $this->modelNamespace,
            '%modelName%' => $this->modelName,
            '%namespaces.mainRepository%' => $this->config('namespaces.main.repository'),
            '%mainRepository%' => $this->config('main.repository'),
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Create Folder & File.
        $fileName = $this->modelName.'Repository';
        $fileDirectory = $this->basePath.'/'.$this->config('paths.repositories');
        $filePath = "{$fileDirectory}/{$fileName}.php";

        // Check if the directory exists, if not create it.
        if (! $this->fileManager->exists($fileDirectory)) {
            $this->fileManager->makeDirectory($fileDirectory, 0755, true);
        }

        // Option to override if file exists.
        $isCreated = true;
        if ($this->laravel->runningInConsole() && $this->fileManager->exists($filePath)) {
            $response = $this->confirm("The repository [{$fileName}] already exists. Do you want to overwrite it?");

            if (! $response) {
                $this->line("The repository [{$fileName}] will not be overwritten.");
                $isCreated = false;
            }
        }

        if ($isCreated) {
            $this->line("The repository [{$fileName}] has been created.");

            $this->fileManager->put($filePath, $content);
        }

        return [
            $this->config('namespaces.repositories').'\\'.$fileName,
            $fileName,
        ];
    }

    /**
     * Create a new Service.
     *
     * @param  string  $contract         Full path of the Interface
     * @param  string  $contractName     The name of the Interface
     * @param  string  $repositoryName   The name of the RepositoryInterface
     * @return array
     */
    protected function createService(
        string $contract,
        string $contractName,
        string $repositoryName
    ): array {
        // Replace Stub's Placeholders.
        $content = $this->fileManager->get($this->stubs['service']);

        $replacements = [
            '%namespaces.services%' => $this->config('namespaces.services'),
            '%namespaces.serviceInterface%' => $contract,
            '%serviceInterface%' => $contractName,
            '%modelName%' => $this->modelName,
            '%namespaces.mainService%' => $this->config('namespaces.main.service'),
            '%mainService%' => $this->config('main.service'),
            '%namespaces.repositories%' => $this->config('namespaces.repositories'),
            '%repositoryName%' => $repositoryName,
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        // Create Folder & File.
        $fileName = $this->modelName.'Service';
        $fileDirectory = $this->basePath.'/'.$this->config('paths.services');
        $filePath = "{$fileDirectory}/{$fileName}.php";

        // Check if the directory exists, if not create it.
        if (! $this->fileManager->exists($fileDirectory)) {
            $this->fileManager->makeDirectory($fileDirectory, 0755, true);
        }

        // Option to override if file exists.
        $isCreated = true;
        if ($this->laravel->runningInConsole() && $this->fileManager->exists($filePath)) {
            $response = $this->confirm("The service [{$fileName}] already exists. Do you want to overwrite it?");

            if (! $response) {
                $this->line("The service [{$fileName}] will not be overwritten.");
                $isCreated = false;
            }
        }

        if ($isCreated) {
            $this->line("The service [{$fileName}] has been created.");

            $this->fileManager->put($filePath, $content);
        }

        return [
            $this->config('namespaces.services').'\\'.$fileName,
            $fileName,
        ];
    }

    /**
     * Check the models existance, create if wanted.
     *
     * @return bool
     */
    protected function checkModel(): bool
    {
        $pathPrefix = '';
        $namespacePrefix = ! $this->isTesting ? 'App\\Models' : '';

        $this->model = str_replace('/', '\\', $this->argument('model'));

        if (! class_exists("{$namespacePrefix}\\{$this->model}") && $this->laravel->runningInConsole()) {
            $response = $this->confirm("Model [{$this->model}] does not exist. Would you like to create it?");

            if ($response) {
                Artisan::call('make:model', [
                    'name' => $this->model,
                ]);

                $pathPrefix = str_replace('/', '\\', app_path()).'\\Models\\';

                if ($this->isTesting) {
                    $namespacePrefix = 'App\\Models';
                }

                $this->line("Model [{$this->model}] has been successfully created.");
            } else {
                $this->line("Model [{$this->model}] is not being created.");

                return false;
            }
        }

        $modelParts = explode('\\', $pathPrefix.$this->model);
        $this->modelName = array_pop($modelParts);
        $this->modelNamespace = $namespacePrefix;

        if ($this->isTesting) {
            $this->modelNamespace .= '\\'.str_replace(
                "\\{$this->modelName}",
                '',
                $this->model
            );
        }

        return true;
    }
}
