<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\SendWhatsappMessegeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;


    private $vendor;
    private $user;
    private $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->user = User::factory(1)->create()->first();
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->user);
        unset($this->userToken);
    }

    public function testGetOrdersForUser()
    {
        Order::factory(11)->for($this->user)->for($this->vendor)->create();

        $res = $this->getJson(route('user.orders.index') . "?token={$this->userToken}");

        $res->assertOk()->assertJsonCount(10, 'data.data');
    }

    public function testValidStoreOrder()
    {
        Notification::fake();
       
        Cart::factory(2)->for($this->vendor)->for($this->user)->create();

        $res = $this->postJson(route('user.orders.store', $this->vendor) . "?token={$this->userToken}");

        $res->assertOk();
        Log::shouldReceive('info');

        Notification::assertSentTo($this->vendor, SendWhatsappMessegeNotification::class);
        Notification::assertSentTo($this->user, SendWhatsappMessegeNotification::class);

        $this
            ->assertDatabaseCount('cart', 0)
            ->assertDatabaseCount('orders', 1);
    }

    public function testStoreOrderForBannedVendor()
    {
        $vendor = Vendor::factory()->create([
            'is_banned' => true
        ]);

        $res = $this->postJson(route('user.orders.store', $vendor) . "?token={$this->userToken}");

        $res->assertForbidden();
    }

    public function testShowOrder()
    {
        $order = Order::factory(1)->for($this->vendor)->for($this->user)->create()->first();

        $res = $this->getJson(route('user.orders.show', $order) . "?token={$this->userToken}");

        $res->assertOk();
    }

    public function testUserCannotShowOtherUserOrder()
    {
        $order = Order::factory(1)->for($this->vendor)->create()->first();

        $res = $this->getJson(route('user.orders.show', $order) . "?token={$this->userToken}");

        $res->assertNotFound();
    }

    public function testUserCannotCancelNonPendingOrder()
    {
        $order = Order::factory(1)->for($this->vendor)->for($this->user)->create([
            'status' => Order::ORDER_APPROVED
        ])->first();

        $res = $this->putJson(route('user.orders.update', $order) . "?token={$this->userToken}");
        $res->assertForbidden();
    }

    public function testUserCannotUpdateOrderThatDoesNotBelongToHim()
    {
        $order = Order::factory(1)->for($this->vendor)->create()->first();

        $res = $this->putJson(route('user.orders.update', $order) . "?token={$this->userToken}");
        $res->assertForbidden();
    }

    public function testUserCanUpdateOrder()
    {
        $order = Order::factory(1)->for($this->vendor)->for($this->user)->create()->first();

        $res = $this->putJson(route('user.orders.update', $order) . "?token={$this->userToken}", [
            'status' => Order::ORDER_CANCELED
        ]);

        $res->assertCreated();
    }

    public function testUserCannotUpdateOrderForBannedVendor()
    {
        $order = Order::factory(1)->for(Vendor::factory()->create(["is_banned" => true]))->for($this->user)->create()->first();

        $res = $this->putJson(route('user.orders.update', $order) . "?token={$this->userToken}", [
            'status' => Order::ORDER_CANCELED
        ]);

        $res->assertForbidden();
    }
}