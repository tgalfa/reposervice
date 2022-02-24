<?php

namespace tgalfa\RepoService\Tests;

use tgalfa\RepoService\Services\AbstractMainService;
use tgalfa\RepoService\Tests\Models\TestModel;

class ServiceTest extends TestCase
{
    /**
     * Test if Service class extends the MainService.
     *
     * @return void
     */
    public function test_instance(): void
    {
        $this->assertInstanceOf(
            AbstractMainService::class,
            $this->testservice
        );
    }

    /**
     * Test {getModel} function.
     *
     * @return void
     */
    public function test_get_model(): void
    {
        $this->assertEquals(
            TestModel::class,
            get_class($this->testservice->getModel())
        );
    }

    /**
     * Test {getById} function.
     *
     * @return void
     */
    public function test_get_by_id(): void
    {
        $model = TestModel::factory()->create();
        $result = $this->testservice->getById($model->id);

        $this->assertNotNull($result);
        $this->assertEquals($model->id, $result->id);
    }

    /**
     * Test {store} function.
     *
     * @return void
     */
    public function test_store(): void
    {
        $result = $this->testservice->store([
            'name' => 'Test',
            'slug' => 'test',
            'type' => 'test',
        ]);

        $this->assertModelExists($result);
    }

    /**
     * Test {update} function.
     *
     * @return void
     */
    public function test_update(): void
    {
        $model = TestModel::factory()->create();

        $data = [
            'name' => 'Test',
            'slug' => 'test',
        ];

        $this->testservice->update($model, $data);

        $this->assertDatabaseHas('test_models', $data);
    }

    /**
     * Test {delete} function.
     *
     * @return void
     */
    public function test_delete(): void
    {
        $model = TestModel::factory()->create();

        $this->testservice->delete($model);

        $this->assertModelMissing($model);
    }
}
