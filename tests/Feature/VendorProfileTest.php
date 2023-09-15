<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauth_vendor_cannot_get_info()
    {
        $res = $this->getJson(route('vendor.user'));

        $res->assertStatus(401);
    }

    public function test_get_vendor_info(): void
    {
        $vendor = Vendor::factory(1)->create()->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->getJson(route('vendor.user') . "?token=$token");

        $res->assertStatus(200);
    }

    public function test_failed_update_vendor_info(): void
    {
        $vendor = Vendor::factory(1)->create([
            'first_name' => 'ahmed'
        ])->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->putJson(route('vendor.updateInfo') . "?token=$token");

        $res->assertStatus(422);
    }

    public function test_update_vendor_info(): void
    {
        $vendor = Vendor::factory(1)->create([
            'first_name' => 'ahmed'
        ])->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->putJson(route('vendor.updateInfo') . "?token=$token", array_merge(
            $vendor->toArray(),
            ['first_name' => 'kareem']
        ));
        $res->assertStatus(201);
        $res->assertJsonPath('data.first_name', 'kareem');
    }

    public function test_failed_vendor_update_password()
    {
        $vendor = Vendor::factory(1)->create()->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->putJson(route('vendor.updatePassword') . "?token=$token");

        $res->assertStatus(422);
        $res->assertJsonPath('data.password.0', 'The password field is required.');
    }

    public function test_vendor_update_password()
    {
        $vendor = Vendor::factory(1)->create()->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->putJson(route('vendor.updatePassword') . "?token=$token", [
            'password' => '12345678',
            'new_password' => '123456789',
            'new_password_confirmation' => '123456789',
        ]);

        $res->assertStatus(201);
    }

    public function test_failed_vendor_update_avatar()
    {
        $vendor = Vendor::factory(1)->create()->first();
        $token = JWTAuth::fromUser($vendor);

        $res = $this->putJson(route('vendor.updateAvatar') . "?token=$token");

        $res->assertStatus(422);
    }

    public function test_vendor_update_avatar()
    {
        $vendor = Vendor::factory(1)->create()->first();
        $token = JWTAuth::fromUser($vendor);

        Storage::fake('public');

        $res = $this->putJson(route('vendor.updateAvatar') . "?token=$token", [
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
        ]);
        Storage::disk('public')->assertExists('vendors/avatars/' . 1  . '/' . ltrim($res->json()['data']['avatar'], '/storage/' . 'vendors/avatars/' . 1  . '/'));

        $res->assertStatus(201);
    }
}
