<?php

namespace tgalfa\RepoService\Tests;

use InvalidArgumentException;
use tgalfa\RepoService\Repositories\AbstractMainRepository;
use tgalfa\RepoService\Tests\Models\TestModel;

class RepositoryTest extends TestCase
{
    /**
     * Test if Respository class extends the MainRepository.
     *
     * @return void
     */
    public function test_instance(): void
    {
        $this->assertInstanceOf(
            AbstractMainRepository::class,
            $this->testrepo
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
            get_class($this->testrepo->getModel())
        );
    }

    /**
     * Test {get} function.
     *
     * @return void
     */
    public function test_get(): void
    {
        TestModel::factory(3)->create();
        $result = $this->testrepo->get();

        $this->assertEquals($result->count(), 3);
    }

    /**
     * Test {get} function with simple Scope parameter.
     *
     * @return void
     */
    public function test_get_scope_simple(): void
    {
        TestModel::factory(3)->create();
        TestModel::factory(2)->create([
            'type' => 'test',
        ]);
        $results = $this->testrepo->get([
            'name',
            'slug',
            'type',
        ], [
            'testType',
        ]);

        $this->assertEquals($results->count(), 2);
    }

    /**
     * Test {get} function with Scope parameter.
     *
     * @return void
     */
    public function test_get_scope_with_param(): void
    {
        TestModel::factory(3)->create();
        TestModel::factory(3)->create([
            'type' => 'test',
        ]);

        $results = $this->testrepo->get([
            'name',
            'slug',
            'type',
        ], [
            'byType' => 'test',
        ]);

        $this->assertEquals($results->count(), 3);
    }

    /**
     * Test {get} function with multiple scope parameters.
     *
     * @return void
     */
    public function test_get_scope_multiple(): void
    {
        TestModel::factory(3)->create();
        TestModel::factory(2)->create([
            'type' => 'test',
        ]);
        TestModel::factory()->create([
            'name' => 'Test1',
            'type' => 'test',
        ]);
        TestModel::factory()->create([
            'name' => 'Test2',
            'type' => 'test',
        ]);

        $results = $this->testrepo->get([
            '*',
        ], [
            'testType',
            'nameStarts' => 'Test',
        ]);

        $this->assertEquals($results->count(), 2);
    }

    /**
     * Test {paginate} function.
     *
     * @return void
     */
    public function test_paginate(): void
    {
        TestModel::factory(8)->create();
        $result = $this->testrepo->paginate(3);

        $this->assertEquals($result->count(), 3);
    }

    /**
     * Test {getById} function.
     *
     * @return void
     */
    public function test_get_by_id(): void
    {
        $model = TestModel::factory()->create();
        $result = $this->testrepo->getById($model->id);

        $this->assertNotNull($result);
        $this->assertEquals($model->id, $result->id);
    }

    /**
     * Test {getById} function returns Null.
     *
     * @return void
     */
    public function test_get_by_id_is_null(): void
    {
        $result = $this->testrepo->getById(42);

        $this->assertNull($result);
    }

    /**
     * Test {store} function.
     *
     * @return void
     */
    public function test_store(): void
    {
        $result = $this->testrepo->store([
            'name' => 'Test',
            'slug' => 'test',
            'type' => 'test',
        ]);

        $this->assertModelExists($result);
    }

    /**
     * Test {store} function Exception.
     *
     * @return void
     */
    public function test_store_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $response = $this->testrepo->store([
            'name' => 'Test',
            'slug' => 'test',
            'wrong' => 'Wrong field data',
        ]);
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

        $this->testrepo->update($model, $data);

        $this->assertDatabaseHas('test_models', $data);
    }

    /**
     * Test {update} function Exception.
     *
     * @return void
     */
    public function test_update_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->testrepo->update(new TestModel(), [
            'wrong' => 'Wrong field data',
        ]);
    }

    /**
     * Test {delete} function.
     *
     * @return void
     */
    public function test_delete(): void
    {
        $model = TestModel::factory()->create();

        $this->testrepo->delete($model);

        $this->assertModelMissing($model);
    }

    /**
     * Test {delete} function Exception.
     *
     * @return void
     */
    public function test_delete_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->testrepo->delete(new TestModel());
    }
}
