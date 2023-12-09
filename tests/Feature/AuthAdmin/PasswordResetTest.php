<?php

namespace Tests\Feature\AuthAdmin;

use App\Models\Admin;
use App\Notifications\SendAdminResetLink;
use App\Notifications\SendAdminResetLinkEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_for_link_can_be_requested_by_admin(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();

        $res = $this->postJson(route('admin.password.email'), ['email' => $admin->email]);

        $res->assertJsonPath('status' , __('passwords.sent'));
        Notification::assertSentTo($admin, SendAdminResetLinkEmail::class);
    }

    public function test_password_can_be_reset_with_valid_token_by_admin(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();

        $this->postJson(route('admin.password.email'), ['email' => $admin->email]);

        Notification::assertSentTo($admin, SendAdminResetLinkEmail::class, function (object $notification) use ($admin) {
            $response = $this->post(route('admin.password.store'), [
                'token' => $notification->token,
                'email' => $admin->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}