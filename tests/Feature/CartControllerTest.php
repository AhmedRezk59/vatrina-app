<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private $vendor;
    private $user;
    private $product;
    private $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->product = Product::factory(1)->create()->first();
        $this->user = User::factory(1)->create()->first();
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->product);
        unset($this->user);
        unset($this->userToken);
    }

    public function testUnauthenticatedUserCannotAddToTheCart()
    {
        $res = $this->postJson(route('user.cart.add', [$this->vendor, $this->product]));
        $res->assertStatus(401);
    }

    public function testUserCannotAddToTheCartBcauseQuantityIsMoreThanProductAmount()
    {
        $res = $this->postJson(route('user.cart.add', [$this->vendor, $this->product]) . "?token={$this->userToken}", [
            'quantity' => $this->product->amount + 1
        ]);

        $res
            ->assertStatus(422);
    }

    public function testUserCanAddToTheCart()
    {
        $res = $this->actingAs($this->user)->postJson(route('user.cart.add', [$this->vendor, $this->product]) . "?token={$this->userToken}", [
            'quantity' => $this->product->amount - 1
        ]);
        $res
            ->assertStatus(200);
        $this
            ->assertDatabaseCount('cart', 1)
            ->assertDatabaseHas('cart', [
                'quantity' => $this->product->amount - 1,
            ]);
    }

    public function testUnauthenticatedUserCannotRemoveFromTheCart()
    {
        $res = $this->deleteJson(route('user.cart.remove', [$this->vendor, $this->product]));
        $res->assertStatus(401);
    }

    public function testUserCanRemoveFromTheCart()
    {
        $this->actingAs($this->user)->postJson(route('user.cart.add', [$this->vendor, $this->product]) . "?token={$this->userToken}", [
            'quantity' => $this->product->amount - 1
        ]);
        $res = $this->deleteJson(route('user.cart.remove', [$this->vendor, $this->product]));
        $res
            ->assertStatus(200);
        $this
            ->assertDatabaseCount('cart', 0);
    }

    public function testUnauthenticatedUserCannotUpdateToTheCart()
    {
        $res = $this->postJson(route('user.cart.update', [$this->vendor, $this->product]));
        $res->assertStatus(401);
    }

    public function testUserCannotUpdateTheCart()
    {
        $this->actingAs($this->user)->postJson(route('user.cart.add', [$this->vendor, $this->product]) . "?token={$this->userToken}", [
            'quantity' => $this->product->amount - 1
        ]);

        $res = $this->postJson(route('user.cart.update', [$this->vendor, $this->product]));

        $res->assertStatus(422);
    }

    public function testUserCanUpdateTheCart()
    {
        $this->actingAs($this->user)->postJson(route('user.cart.add', [$this->vendor, $this->product]) . "?token={$this->userToken}", [
            'quantity' => $this->product->amount - 1
        ]);

        $res = $this->postJson(route('user.cart.update', [$this->vendor, $this->product]), [
            'quantity' => $this->product->amount
        ]);

        $res->assertOk();
        $this->assertDatabaseHas('cart' , [
            'id' => $this->product->id,
            'quantity' => $this->product->amount
        ]);
    }
}