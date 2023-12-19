<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Events\NewAdminRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRegisterationRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(AdminRegisterationRequest $request): JsonResponse
    {
        $path = Storage::disk('public')->put('/admins/avatars/', $request->avatar);

        $admin = Admin::create(collect([...$request->validated(), 'avatar' => $path])->except('permissions')->toArray());
        $admin->givePermissions($request->permissions ?: []);

        event(new NewAdminRegistered($admin, 'general', 'You have signed up for ' . config('app.name')));

        Log::info("New Admin signed up with an email {$admin->email}");

        $token = JWTAuth::fromUser($admin);

        return $this->apiResponse(
            [
                'token' => $token,
                'data' => AdminResource::make($admin)
            ]
        );
    }
}