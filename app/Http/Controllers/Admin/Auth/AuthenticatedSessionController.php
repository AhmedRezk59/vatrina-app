<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return $this->apiResponse(
                status: 'error',
                msg: 'No Admin in our database with this email.',
                code: 400
            );
        } elseif (!Hash::check($request->password, $admin->password)) {
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
                    'token' => JWTAuth::fromUser($admin),
                    'data' => AdminResource::make($admin)
                ]
            );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('api-admin')->logout();

        return $this->apiResponse(
            msg: 'You logged out successfully',
            code: 200,
        );
    }
}