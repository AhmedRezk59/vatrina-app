<?php

namespace Tests\Feature;

use App\Events\SendMessageToUser;
use App\Events\SendVendorAMessage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    private $vendor;
    private $user;
    private $vendorToken;
    private $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = Vendor::factory(1)->create()->first();
        $this->user = User::factory(1)->create()->first();
        $this->vendorToken = JWTAuth::fromUser($this->vendor);
        $this->userToken = JWTAuth::fromUser($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->vendor);
        unset($this->user);
        unset($this->vendorToken);
        unset($this->userToken);
    }

    public function testUserCannotSendVendorAMessage()
    {
        $res = $this->postJson(route("user.send.message", $this->vendor) . "?token={$this->userToken}");
        $res
            ->assertStatus(422);
    }

    public function testUserCanSendVendorAMessage()
    {
        Event::fake();
        $res = $this->postJson(route("user.send.message", $this->vendor) . "?token={$this->userToken}", [
            'msg' => "HI"
        ]);

        Event::assertDispatched(SendVendorAMessage::class);

        $res
            ->assertStatus(200);
    }

    public function testVendorCannotSendUserAMessage()
    {
        $res = $this->postJson(route("vendor.send.message", $this->user) . "?token={$this->vendorToken}");
        $res
            ->assertStatus(422);
    }

    public function testVendorCanSendUserAMessage()
    {
        Event::fake();
        $res = $this->postJson(route("vendor.send.message", $this->user) . "?token={$this->vendorToken}", [
            'msg' => "HI"
        ]);

        Event::assertDispatched(SendMessageToUser::class);

        $res
            ->assertStatus(200);
    }
}
