<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserVendorControllerTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
    }

    public function testUserCanAccessAllVendorProductsThatBelongToVendor()
    {
        Product::factory(5)->for($this->vendor)->create();
        Product::factory(5)->for(Vendor::factory(1)->create()->first())->create();
        $res = $this->getJson(route('user.vendor.interface.products',[$this->vendor->username]));

        $res
            ->assertOk()
            ->assertJson(function (AssertableJson $json ){
                $json->has('data.data',5)->etc();
            });
    }

    public function testUserCanAccessVendorProductsForSpecificCollection()
    {
        $collections = Collection::factory(2)->for($this->vendor)->create();
        Product::factory(5)->for($this->vendor)->for($collections->first())->create();
        Product::factory(5)->for($this->vendor)->for($collections->last())->create();
        $res = $this->getJson(route('user.vendor.interface.products',$this->vendor->username) . "?filter[collection_id]={$collections->first()->id}");

        $res
            ->assertOk()
            ->assertJson(function (AssertableJson $json ){
                $json->has('data.data',5)->etc();
            });
    }

    public function testUserCanAccessAllVendorCollectionsThatBelongToVendor()
    {
        Collection::factory(2)->for($this->vendor)->create();
        Collection::factory(2)->for(Vendor::factory(1)->create()->first())->create();
        $res = $this->getJson(route('user.vendor.interface.collections',[$this->vendor->username]));

        $res
            ->assertOk()
            ->assertJson(function (AssertableJson $json ){
                $json->has('data',2)->etc();
            });
    }

    public function testUserCannotGetProductInformationForFalseVendor(){
        $product = Product::factory(1)->for(Vendor::factory(1)->create()->first())->create()->first();

        $res = $this->getJson(route('user.vendor.interface.product' , [$this->vendor->username , $product->id]));
        $res
            ->assertForbidden();
    }

    public function testUserGetProductInformation(){
        $product = Product::factory(1)->for($this->vendor)->create()->first();

        $res = $this->getJson(route('user.vendor.interface.product' , [$this->vendor->username , $product->id]));
        $res
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data','msg'])->etc();
            });
    }
}
