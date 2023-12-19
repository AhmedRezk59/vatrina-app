<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendorOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;
    private Order $order;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->order = Order::factory(1)->create()->first();
        $this->token = JWTAuth::fromUser($this->vendor);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->order);
        unset($this->token);
    }

    public function testFailedUpdateOrderStatus(): void
    {
        $response = $this->putJson(route('vendor.updateOrderStatus', $this->order) . "?token={$this->token}");

        $response->assertStatus(422);
    }

    public function testValidUpdateOrderStatus(): void
    {
        $response = $this->putJson(route('vendor.updateOrderStatus', $this->order) . "?token={$this->token}", [
            'status' => Order::ORDER_APPROVED
        ]);

        Log::shouldReceive('info')->with("Order status got updated to " . Order::ORDER_APPROVED);
        
        $response->assertStatus(201);
    }
}