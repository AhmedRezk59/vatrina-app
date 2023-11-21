<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
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
        $vendor = Vendor::where('email', $request->email)->first();
        if (!$vendor) {
            return $this->apiResponse(
                status: 'error',
                msg: 'No Vendor in our database with this email.',
                code: 400
            );
        } elseif (!Hash::check($request->password, $vendor->password)) {
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
                    'token' => JWTAuth::fromUser($vendor),
                    'data' => VendorResource::make($vendor)
                ]
            );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('api-vendor')->logout();

        return $this->apiResponse(
            msg: 'You logged out successfully',
            code: 200,
        );
    }
}