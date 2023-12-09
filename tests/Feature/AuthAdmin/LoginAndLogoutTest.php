<?php

namespace Feature\AuthAdmin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginAndLogoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_invalid_login_for_admin(): void
    {
        $response = $this->postJson(route('admin.login'));

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['status', 'data', 'msg']);
        });
    }

    public function test_admin_can_login()
    {
        $admin = Admin::factory(1)->create()->first();
        $res = $this->postJson(route('admin.login'), [
            'email' => $admin->email,
            'password' => '12345678'
        ]);

        $res->assertStatus(200);
    }

    public function test_invalid_logout_for_admin(): void
    {
        $response = $this->postJson(route('admin.logout'));

        $response->assertStatus(401);
    }

    public function test_admin_can_logout()
    {
        $admin = Admin::factory(1)->create()->first();

        $res = $this->actingAs($admin)->postJson(
            uri: route('admin.logout'),
            headers: [
                'Authorization' => "Bearer " . JWTAuth::fromUser($admin)
            ]
        );
        $res->assertStatus(200);
    }
}