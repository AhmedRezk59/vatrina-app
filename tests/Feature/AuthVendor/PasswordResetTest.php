<?php

namespace Tests\Feature\AuthVendor\Auth;

use App\Models\Vendor;
use App\Notifications\SendUserResetLink;
use App\Notifications\SendVendorResetLink;
use App\Notifications\SendVendorResetLinkEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_for_link_can_be_requested_by_vendor(): void
    {
        Notification::fake();

        $vendor = Vendor::factory()->create();

        $res = $this->postJson(route('vendor.password.email'), ['email' => $vendor->email]);

        $res->assertJsonPath('status', __('passwords.sent'));
        Notification::assertSentTo($vendor, SendVendorResetLinkEmail::class);
    }

    public function test_password_can_be_reset_with_valid_token_by_vendor(): void
    {
        Notification::fake();

        $vendor = Vendor::factory()->create();

        $this->postJson(route('vendor.password.email'), ['email' => $vendor->email]);

        Notification::assertSentTo($vendor, SendVendorResetLinkEmail::class, function (object $notification) use ($vendor) {
            $response = $this->postJson(route('vendor.password.store'), [
                'token' => $notification->token,
                'email' => $vendor->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}