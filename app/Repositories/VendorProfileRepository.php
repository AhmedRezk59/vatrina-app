<?php

namespace App\Repositories;

use App\Contracts\VendorProfileContract;
use App\Models\Vendor;
use App\Traits\WithApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorProfileRepository implements VendorProfileContract
{
    use WithApiResponse;

    public function updateVendorInfo($request): Vendor
    {
        $vendor = $request->user('api-vendor');
        $vendor->update($request->validated());
        return $vendor->fresh();
    }

    public function updatePassword($request): Vendor
    {
        $vendor = $request->user('api-vendor');

        if (!Hash::check($request->password, $vendor->password)) {
            throw new HttpResponseException(
                $this->apiResponse(
                    status: 'error',
                    msg: 'Invalid credentials.',
                    code: 400
                )
            );
        }

        $vendor->update([
            'password' => $request->new_password
        ]);

        return $vendor;
    }

    public function updateAvatar($request): Vendor
    {
        $vendor = $request->user('api-vendor');

        Storage::disk('public')->delete($vendor->avatar);

        $path = Storage::disk('public')->put('/vendors/avatars/', $request->file('avatar'));

        $vendor->update([
            'avatar' => $path
        ]);

        return $vendor->fresh();
    }
}