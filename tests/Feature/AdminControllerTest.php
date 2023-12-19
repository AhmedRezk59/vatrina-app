<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory(1)->create()->first();
        $this->token = JWTAuth::fromUser($this->admin);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->admin);
        unset($this->token);
    }

    public function test_get_admins(): void
    {
        Admin::factory(11)->create();

        $response = $this->getJson(route('admin.admins.index') . "?token={$this->token}");

        $response
            ->assertJsonCount(10, 'data')
            ->assertStatus(200);
    }

    public function test_show_specific_admin()
    {
        $response = $this->getJson(route('admin.admins.show', $this->admin) . "?token={$this->token}");

        $response
            ->assertOk()
            ->assertJsonPath('data.email', $this->admin->email);
    }

    public function test_update_specific_admin()
    {
        Storage::fake('public');
        Event::fake();
        $avatar = UploadedFile::fake()->image('hi.png', 350, 250);

        $admin = [
            "first_name" => "Gerda",
            "last_name" => "Bechtelar",
            "username" => "Bechtelar",
            "email" => "sally88@yahoo.com",
            "phone_number" => "0101028471",
            "avatar" => $avatar,
            "password" => "NK[fOy;3$\'J",
            "password_confirmation" => "NK[fOy;3$\'J"
        ];

        $res = $this->postJson(route('admin.register'), $admin);
        $avatar2 = UploadedFile::fake()->image('hi.png', 350, 250);

        $response = $this->putJson(route('admin.admins.update', 'Bechtelar') . "?token={$res['data']['token']}", [
            'first_name' => 'Ahmed',
            'last_name' => 'Mohamed',
            'email' => "a@a.com",
            'username' => 'asd',
            'phone_number' => '1-2312309-323',
            'avatar' => $avatar2
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ahmed Mohamed');
        Storage::disk('public')->assertMissing('admins/avatars/' . $avatar->hashName());
        Storage::disk('public')->assertExists('admins/avatars/' . $avatar2->hashName());
        $this->get($response['data']['avatar'])->assertOk();
    }

    public function test_admin_cannot_update_another_admin()
    {
        $admin = Admin::factory()->create();

        $response = $this->putJson(route('admin.admins.update', $admin) . "?token={$this->token}", [
            'first_name' => 'Ahmed',
            'last_name' => 'Mohamed',
            'email' => "a@a.com",
            'username' => 'asd',
            'phone_number' => '1-2312309-323',
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_himself()
    {
        $res = $this->deleteJson(route('admin.admins.destroy', $this->admin) . "?token={$this->token}");
        $res->assertOk();
    }

    public function test_admin_cannot_delete_another_admin_without_having_the_rights()
    {
        $admin = Admin::factory()->create();
        $res = $this->deleteJson(route('admin.admins.destroy', $admin) . "?token={$this->token}");
        $res->assertForbidden();
    }

    public function test_admin_can_delete_another_admin_by_having_the_rights()
    {
        Permission::create([
            'name' => 'delete_admins'
        ]);
        $admin = Admin::factory()->create();
        $this->admin->givePermission(
            'delete_admins'
        );
        $res = $this->actingAs($this->admin)->deleteJson(route('admin.admins.destroy', $admin) . "?token={$this->token}");
        $res->assertOk();
    }
}