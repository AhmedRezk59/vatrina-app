<?php

namespace App\Http\Controllers\User\Auth;

use App\Events\NewUserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
    public function store(UserRegistrationRequest $request): JsonResponse
    {
        $user = User::create([...$request->validated() , 'avatar' => $request->file("avatar")->hashName()]);

        Storage::disk('public')->put('/users/avatars/' . $user->id, $request->avatar);

        event(new NewUserRegistered($user, 'new_user_registered' ,'You have signed up for ' . config('app.name')));

        Log::info("New User signed up with an email {$user->email}");

        $token = JWTAuth::fromUser($user);

        return $this->apiResponse(
            [
                'token' => $token,
                'data' => UserResource::make($user)
            ]
        );
    }
}
