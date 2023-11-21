<?php

namespace Tests\Feature\AuthUser\Auth;

use App\Models\User;
use App\Notifications\SendUserResetLink;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_for_link_can_be_requested_by_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $res = $this->postJson(route('user.password.email'), ['email' => $user->email]);

        $res->assertJsonPath('status' , __('passwords.sent'));
        Notification::assertSentTo($user, SendUserResetLink::class);
    }

    public function test_password_can_be_reset_with_valid_token_by_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('user.password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, SendUserResetLink::class, function (object $notification) use ($user) {
            $response = $this->post(route('user.password.store'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}