<?php

namespace Tests\Feature;

use App\Events\NewVendorRegistered;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendorProfileTest extends TestCase
{
    use RefreshDatabase;

    private Vendor $vendor;
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

    public function test_unauth_vendor_cannot_get_info()
    {
        $res = $this->getJson(route('vendor.user'));

        $res->assertStatus(401);
    }

    public function test_get_vendor_info(): void
    {
        $res = $this->getJson(route('vendor.user') . "?token=$this->token");

        $res->assertStatus(200);
    }

    public function test_failed_update_vendor_info(): void
    {
        $res = $this->putJson(route('vendor.updateInfo') . "?token=$this->token");

        $res->assertStatus(422);
    }

    public function test_update_vendor_info(): void
    {

        $res = $this->actingAs($this->vendor)->putJson(route('vendor.updateInfo') . "?token=$this->token", array_merge(
            $this->vendor->toArray(),
            ['first_name' => 'ahmed']
        ));
        $res->assertStatus(201);
        $res->assertJsonPath('data.first_name', 'ahmed');
    }

    public function test_failed_vendor_update_password()
    {
        $res = $this->putJson(route('vendor.updatePassword') . "?token=$this->token");

        $res->assertStatus(422);
        $res->assertJsonPath('data.password.0', 'The password field is required.');
    }

    public function test_vendor_update_password()
    {
        $res = $this->actingAs($this->vendor)->putJson(route('vendor.updatePassword') . "?token=$this->token", [
            'password' => '12345678',
            'new_password' => '123456789',
            'new_password_confirmation' => '123456789',
        ]);

        $res->assertStatus(201);
    }

    public function test_failed_vendor_update_avatar()
    {
        $res = $this->putJson(route('vendor.updateAvatar') . "?token=$this->token");

        $res->assertStatus(422);
    }

    public function test_vendor_update_avatar()
    {
        Storage::fake('public');
        Event::fake([NewVendorRegistered::class]);
        
        $avatar = UploadedFile::fake()->image('hi.png', 200, 200);
        $vendor = [
            "first_name" => "Freeman",
            "last_name" => "Murray",
            "email" => "stacy52@corwin.com",
            "username" => "Elias",
            "phone_number" => "+1-858-539-2720",
            'avatar' => $avatar,
            "password" => "12345678",
            "password_confirmation" => "12345678",
        ];

        $registerResponse = $this->postJson(
            route('vendor.register'),
            $vendor
        );
       
        $avatar2 = UploadedFile::fake()->image('hi.png', 350, 300);
        $updateResponse = $this->actingAs($this->vendor)->putJson(route('vendor.updateAvatar') . "?token={$registerResponse->json('data')['token']}", [
            "avatar" => $avatar2,
        ]);
        $this->get($updateResponse->json()['data']['avatar'])->assertOk();
        Storage::disk('public')->assertMissing("/vendors/avatars/{$avatar->hashName()}");
        Storage::disk('public')->assertExists("/vendors/avatars/{$avatar2->hashName()}");
        $updateResponse->assertStatus(201);
    }
}