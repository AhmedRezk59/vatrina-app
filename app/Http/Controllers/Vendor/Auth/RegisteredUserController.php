<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:55', 'unique:' . Vendor::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . Vendor::class],
            'phone_number' => ['required', 'string', 'max:20', 'min:8'],
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg', 'dimensions:min_width=150,min_height=150,max_width=500,max_height=500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        $vendor = Vendor::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'avatar' => $request->avatar,
            'password' => Hash::make($request->password),
        ]);

        Storage::disk('public')->put('/vendors/avatars/' . $vendor->id, $request->avatar);
        event(new Registered($vendor));

        $token = JWTAuth::fromUser($vendor);

        return $this->apiResponse(
            [
                'token' => $token,
                'data' => VendorResource::make($vendor)
            ]
        );
    }
}
