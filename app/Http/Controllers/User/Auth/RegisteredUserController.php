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
        $path = Storage::disk('public')->put('/users/avatars/', $request->avatar);
        
        $user = User::create([...$request->validated() , 'avatar' => $path]);


        event(new NewUserRegistered($user, 'general' ,'You have signed up for ' . config('app.name')));

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