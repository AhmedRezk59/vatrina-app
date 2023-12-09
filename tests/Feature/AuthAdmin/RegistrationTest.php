<?php

namespace Tests\Feature\AuthAdmin;

use App\Events\NewAdminRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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
        $this->get(route('admin.interface.avatar', "Bechtelar"))->assertOk();
    }
}