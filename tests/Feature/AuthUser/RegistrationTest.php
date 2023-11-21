<?php

namespace Tests\Feature\AuthUser\Auth;

use App\Events\NewUserRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_register_for_user(): void
    {
        $response = $this->postJson(route('user.register'));

        $response->assertStatus(422);
    }

    public function test_user_can_register()
    {
        Storage::fake('public');
        Event::fake();

        $user = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => UploadedFile::fake()->image('hi.png', 350, 300),
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J"
        ];

        $res = $this->postJson(route('user.register'), $user);

        $res->assertStatus(200);

        $this->assertNotNull($res->json('data')['token']);
        $this->assertDatabaseHas('users', ['email' => $user['email']]);
        Log::shouldReceive('info');
        Event::assertDispatched(NewUserRegistered::class);
        $this->get(route('user.user.interface.avatar', "Bechtelar"))->assertOk();
    }
}