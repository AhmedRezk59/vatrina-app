<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->apiResponse(
                status: 'error',
                msg: 'No User in our database with this email.',
                code: 400
            );
        } elseif (!Hash::check($request->password, $user->password)) {
            return $this->apiResponse(
                status: 'error',
                msg: 'Invalid credentials.',
                code: 400
            );
        }

        return
            $this->apiResponse(
                msg: 'You logged in successfully',
                code: 200,
                data: [
                    'token' => JWTAuth::fromUser($user),
                    'data' => UserResource::make($user)
                ]
            );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::logout();

        return $this->apiResponse(
            msg: 'You logged out successfully',
            code: 200,
        );
    }
}