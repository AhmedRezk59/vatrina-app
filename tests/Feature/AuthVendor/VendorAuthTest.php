<?php

namespace Tests\Feature\AuthVendor;

use App\Events\NewVendorRegistered;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPSTORM_META\type;

class VendorAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_register_for_vendor(): void
    {
        $response = $this->postJson(route('vendor.register'));

        $response->assertStatus(422);
    }

    public function test_vendor_can_register()
    {
        Storage::fake('public');
        Event::fake();
        $vendor = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J"
        ];

        $res = $this->postJson(route('vendor.register'), $vendor);

        $res->assertStatus(200);
        Event::assertDispatched(NewVendorRegistered::class);
        Log::shouldReceive('info');
        $this->assertDatabaseHas('vendors', ['email' => $vendor['email']]);
        Storage::disk('public')->assertExists('vendors/avatars/' . 1  . '/' . $vendor['avatar']->hashName());
    }

    public function test_invalid_login_for_vendor(): void
    {
        $response = $this->postJson(route('vendor.login'));

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['status', 'data', 'msg']);
        });
    }

    public function test_vendor_can_login()
    {
        $vendor = Vendor::factory(1)->create();
        $res = $this->postJson(route('vendor.login'), [
            'email' => $vendor->first()->email,
            'password' => '12345678'
        ]);

        $res->assertStatus(200);
    }

    public function test_invalid_logout_for_vendor(): void
    {
        $response = $this->postJson(route('vendor.logout'));

        $response->assertStatus(401);
    }

    public function test_vendor_can_logout()
    {
        $vendor = Vendor::factory(1)->create()->first();

        $res = $this->postJson(
            uri: route('vendor.logout'),
            headers: [
                'Authorization' => "Bearer " . JWTAuth::fromUser($vendor)
            ]
        );
        $res->assertStatus(200);
    }
}
