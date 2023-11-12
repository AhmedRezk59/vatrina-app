<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CollectionControllerTest extends TestCase
{
    use RefreshDatabase;

    private $vendor;
    private $token;
    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->token = JWTAuth::fromUser($this->vendor);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->token);
    }

    public function test_get_paginated_collections(): void
    {
        Collection::factory(11)->for($this->vendor)->create();

        $response = $this->getJson(route('vendor.collections.index') . "?token={$this->token}");

        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('data.data', 10)->etc();
        });
    }

    public function test_valid_store_collection(): void
    {
        $response = $this->postJson(route('vendor.collections.store') . "?token={$this->token}", [
            'name' => 'Anything'
        ]);

        $response->assertStatus(201);
    }

    public function test_in_valid_store_collection(): void
    {
        $response = $this->postJson(route('vendor.collections.store') . "?token={$this->token}");

        $response->assertStatus(422);
    }

    public function test_show_collection_method()
    {
        $collection = Collection::factory(1)->create()->first();

        $response = $this->getJson(route('vendor.collections.show', $collection->id) . "?token={$this->token}");

        $response->assertJsonPath('data.name', $collection->name);
        $response->assertJsonPath('data.id', $collection->id);
        $response->assertStatus(200);
    }

    public function test_update_collection_method()
    {
        $collection = Collection::factory(1)->create()->first();
        $newName = 'new name';

        $response = $this->putJson(route('vendor.collections.update', $collection->id) . "?token={$this->token}", [
            'name' => $newName
        ]);

        $response->assertJsonPath('data.name', $newName);
        $response->assertJsonPath('data.id', $collection->id);
        $response->assertStatus(201);
    }

    public function test_invalid_update_collection_method()
    {
        $collection = Collection::factory(1)->create()->first();

        $response = $this->putJson(route('vendor.collections.update', $collection->id) . "?token={$this->token}");

        $response->assertStatus(422);
    }

    public function test_destroy_collection_method()
    {
        $collection = Collection::factory(1)->create()->first();

        $response = $this->deleteJson(route('vendor.collections.destroy', $collection->id) . "?token={$this->token}");

        $this->assertDatabaseCount('collections', 0);
        $response->assertStatus(200);
    }
}
