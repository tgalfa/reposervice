<?php

namespace tgalfa\RepoService\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\PendingCommand;
use tgalfa\RepoService\RepoServiceServiceProvider;
use tgalfa\RepoService\Tests\Models\TestModel;
use tgalfa\RepoService\Tests\Repository\TestRepository;
use tgalfa\RepoService\Tests\Service\TestService;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * The Test Repository.
     *
     * @var \tgalfa\RepoService\Repositories\AbstractMainRepository
     */
    protected $testrepo;

    /**
     * The Test Service.
     *
     * @var \tgalfa\RepoService\Services\AbstractMainService
     */
    protected $testservice;

    /**
     * New Test Model Name.
     *
     * @return string
     */
    protected $testNewModel = 'tgalfa\\tests\\TestNewModel';

    /**
     * Run migration.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create Test database table.
        $this->createTestModelTable();

        // Init Test Repo.
        $this->testrepo = new TestRepository();

        // Init Test Service.
        $this->testservice = new TestService();
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        // Drop Test database table.
        $this->dropTestModelTable();
        // Delete Tests/App folder and its files.
        $this->deleteTestFolder();
        // Delete App/Models/tgalfa/test folder and its files.
        $this->deleteAppTestFolder();

        parent::tearDown();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            RepoServiceServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Define your environment setup.
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $packageconf = include './config/reposervice.php';
        foreach ($packageconf as $key => $value) {
            $app['config']->set('reposervice.'.$key, $value);
        }
    }

    /**
     * Run Console Command.
     *
     * @param  string|null  $model
     * @return \Illuminate\Testing\PendingCommand
     */
    protected function runConsoleCommand(string $model = null): PendingCommand
    {
        return $this->artisan('reposervice:generate', [
            'model' => $model ?? TestModel::class,
        ]);
    }

    /**
     * Get Test Class Names.
     *
     * @return array
     */
    protected function getTestClassNames(): array
    {
        $modelName = 'TestModel';

        $repository = "{$modelName}Repository";
        $repositoryInterface = "{$repository}Interface";

        $service = "{$modelName}Service";
        $serviceInterface = "{$service}Interface";

        return [
            'interfaces' => [
                'repository' => $repositoryInterface,
                'service' => $serviceInterface,
            ],
            'modelName' => $modelName,
            'repository' => $repository,
            'service' => $service,
        ];
    }

    /**
     * Get New Test Model Name.
     *
     * @return string
     */
    protected function getTestNewModelName(): string
    {
        return $this->testNewModel;
    }

    /**
     * Create Test database table.
     *
     * @return void
     */
    private function createTestModelTable(): void
    {
        Schema::create('test_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Drop Test database table.
     *
     * @return void
     */
    private function dropTestModelTable(): void
    {
        Schema::dropIfExists('test_models');
    }

    /**
     * Delete Tests/App folder and its files.
     * The folder is created during Console Command Test.
     *
     * @return void
     */
    private function deleteTestFolder(): void
    {
        File::deleteDirectory('tests/app');
    }

    /**
     * Delete  App/Models/tgalfa/test folder and its files.
     * The folder is created during Console Command Test.
     *
     * @return void
     */
    private function deleteAppTestFolder(): void
    {
        File::deleteDirectory(app()->basePath().'/app/Models/tgalfa');
    }
}
