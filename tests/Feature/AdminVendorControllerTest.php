<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminVendorControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->token = JWTAuth::fromUser($this->admin);
        Permission::create([
            'name' => 'ban-vendor',
            'display_name' => 'Ban vendor', // optional
            'description' => 'Admin can ban vendors.', // 
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->admin);
        unset($this->token);
    }

    public function testUnAuthenticatedAdminCannotBanVendor(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->putJson(route("admin.ban.vendor", $vendor));

        $response->assertStatus(401);
    }
    
    public function testAdminWithPermissionCanBanVendor(): void
    {
        $this->admin->givePermission('ban-vendor');

        $vendor = Vendor::factory()->create();
        
        $response = $this->putJson(route("admin.ban.vendor", $vendor) . "?token={$this->token}");
        
        Log::shouldReceive('info')->with("Vendor {$vendor->username} got banned successfully");
        $this->assertEquals(true, $vendor->fresh()->is_banned);
        $response->assertStatus(200);
    }

    public function testAdminWithoutPermissionCanBanVendor(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->putJson(route("admin.ban.vendor", $vendor) . "?token={$this->token}");

        $response->assertStatus(403);
    }
}