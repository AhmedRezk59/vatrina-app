<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Events\NewVendorRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRegistrationRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
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
    public function store(VendorRegistrationRequest $request): JsonResponse
    {
        $vendor = Vendor::create([...$request->validated() , 'avatar' => $request->file('avatar')->hashName()]);

        Storage::disk('public')->put('/vendors/avatars/' . $vendor->id, $request->avatar);

        event(new NewVendorRegistered($vendor, 'general', 'You have signed up for ' . config('app.name')));

        Log::info("New Vendor signed up with an email {$vendor->email}");

        $token = JWTAuth::fromUser($vendor);

        return $this->apiResponse(
            [
                'token' => $token,
                'data' => VendorResource::make($vendor)
            ]
        );
    }
}
