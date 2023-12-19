<?php

namespace Tests\Feature\AuthAdmin;

use App\Events\NewAdminRegistered;
use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_register_for_admin(): void
    {
        $response = $this->postJson(route('admin.register'));

        $response->assertStatus(422);
    }

    public function test_admin_can_register()
    {
        Storage::fake('public');
        Event::fake();

        $admin = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J"
        ];

        $res = $this->postJson(route('admin.register'), $admin);

        $res->assertStatus(200);

        $this->assertNotNull($res->json('data')['token']);
        $this->assertDatabaseHas('admins', ['email' => $admin['email']]);
        Log::shouldReceive('info');
        Event::assertDispatched(NewAdminRegistered::class);
        $admin = Admin::first();
        $token = JWTAuth::fromUser($admin);
        $this->actingAs($admin)->get(route('admin.interface.avatar', "Bechtelar") . "?token={$token}")->assertOk();
    }

    public function test_admin_cannot_register_with_permissions_that_does_not_exist()
    {
        Storage::fake('public');
        Event::fake();

        $admin = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J",
            'permissions' => ['ban-vendor']
        ];

        $res = $this->postJson(route('admin.register'), $admin)->assertStatus(422);
    }

    public function test_admin_can_register_with_permissions_that_exists()
    {
        Storage::fake('public');
        Event::fake();
        Permission::create([
            'name' => "ban-vendor"
        ]);
        
        $admin = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J",
            'permissions' => ['ban-vendor']
        ];

        $res = $this->postJson(route('admin.register'), $admin)->assertStatus(200);
    }
}