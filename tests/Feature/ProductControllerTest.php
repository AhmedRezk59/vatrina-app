<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $vendor;
    private $collection;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->collection = Collection::factory(1)->create()->first();
        $this->token = JWTAuth::fromUser($this->vendor);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->token);
        unset($this->collection);
    }

    public function test_get_paginated_products(): void
    {
        Product::factory(11)->for($this->vendor)->create();

        $response = $this->getJson(route('vendor.products.index', $this->vendor) . "?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', 10)
                ->etc();
        });
    }

    public function test_valid_store_product(): void
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('hi.png', 200, 200);
        $response = $this->postJson(
            route('vendor.products.store') . "?token={$this->token}",
            Product::factory(1)->for($this->collection)->for($this->vendor)->make(['image' => $image
            ])->first()->toArray()
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.collection.id', $this->collection->id)
            ->assertJsonPath('data.collection.name', $this->collection->name);

        $this->assertDatabaseCount('products', 1);

        Storage::disk('public')->assertExists('vendors/products/' . $this->vendor->id . '/' . $image->hashName());
    }

    public function test_in_valid_store_product(): void
    {
        $response = $this->postJson(route('vendor.products.store') . "?token={$this->token}");

        $response->assertStatus(422);
    }

    public function test_show_method()
    {
        $product = Product::factory(1)->create()->first();

        $response = $this->getJson(route('vendor.products.show', $product->id) . "?token={$this->token}");

        $response->assertJsonPath('data.name', $product->name);
        $response->assertJsonPath('data.id', $product->id);
        $response->assertStatus(200);
    }

    public function test_update_product_method()
    {
        Storage::fake('public');
        $image1 = UploadedFile::fake()->image('hi.png', 200, 200);
        $product = Product::factory(1)->for($this->collection)->for($this->vendor)->make(['image' => $image1
        ])->first()->toArray();

        $storeResponse = $this->postJson(
            route('vendor.products.store') . "?token={$this->token}",
            $product
        );

        $newName = 'new name';
        $image2 = UploadedFile::fake()->image('hi.png', 300, 250);
        $updateResponse = $this->putJson(route('vendor.products.update', 1) . "?token={$this->token}", [
            ...$product,
            'name' => $newName,
            'image' => $image2
        ]);

        Storage::disk('public')->assertMissing('vendors/products/' . $this->vendor->id . '/' . $image1->hashName());

        Storage::disk('public')->assertExists('vendors/products/' . $this->vendor->id . '/' . $image2->hashName());

        $updateResponse->assertJsonPath('data.name', $newName);
        $updateResponse->assertStatus(201);
    }

    public function test_Not_Owner_of_the_product_update_product_method()
    {
        Storage::fake('public');

        $vendor = Vendor::factory()->create();
        $product = Product::factory(1)->for($this->vendor)->make([
            'image' => UploadedFile::fake()->image('hi.png', 200, 200)
        ])->first()->toArray();

        $this->postJson(
            route('vendor.products.store') . "?token={$this->token}",
            $product
        );
        $token = JWTAuth::fromUser($vendor);

        $newName = 'new name';
        unset($product['vendor_id']);

        $updateResponse = $this->actingAs($vendor)->putJson(route('vendor.products.update', 1) . "?token=$token", [

            ...$product,
            'name' => $newName,
            'image' => UploadedFile::fake()->image('hi.png', 300, 250)
        ]);


        $updateResponse->assertStatus(401);
    }

    public function test_destroy_method()
    {
        Storage::fake('public');

        $product = Product::factory(1)->for($this->collection)->for($this->vendor)->make([
            'image' => UploadedFile::fake()->image('hi.png', 200, 200)
        ])->first()->toArray();

        $storeResponse = $this->postJson(
            route('vendor.products.store') . "?token={$this->token}",
            $product
        );

        $deleteResponse = $this->actingAs($this->vendor)->deleteJson(route('vendor.products.destroy', 1) . "?token={$this->token}");

        Storage::disk('public')->assertMissing(ltrim($storeResponse->json('data')['image'], 'storage/'));
        $deleteResponse->assertOk();
        $this->assertDatabaseCount('products', 0);
    }
}