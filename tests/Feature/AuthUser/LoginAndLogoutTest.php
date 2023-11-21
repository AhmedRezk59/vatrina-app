<?php

namespace Feature\AuthUser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginAndLogoutTest extends TestCase
{
    use RefreshDatabase;
    public function test_invalid_login_for_user(): void
    {
        $response = $this->postJson(route('user.login'));

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['status', 'data', 'msg']);
        });
    }

    public function test_user_can_login()
    {
        $user = User::factory(1)->create();
        $res = $this->postJson(route('user.login'), [
            'email' => $user->first()->email,
            'password' => '12345678'
        ]);

        $res->assertStatus(200);
    }

    public function test_invalid_logout_for_user(): void
    {
        $response = $this->postJson(route('user.logout'));

        $response->assertStatus(401);
    }

    public function test_user_can_logout()
    {
        $user = User::factory(1)->create()->first();

        $res = $this->postJson(
            uri: route('user.logout'),
            headers: [
                'Authorization' => "Bearer " . JWTAuth::fromUser($user)
            ]
        );
        $res->assertStatus(200);
    }
}