<?php

namespace App\Services;

use App\Models\Vendor;
use App\Traits\WithApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorProfileService
{
    use WithApiResponse;
    public function updateVendorInfo($request): Vendor
    {
        $vendor = Vendor::find($request->user('api-vendor')->id);
        $vendor->update($request->validated());
        return $vendor->fresh();
    }

    public function updatePassword($request): Vendor
    {
        $vendor = Vendor::where('id', $request->user('api-vendor')->id)->first();

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
            'password' => Hash::make($request->new_password)
        ]);

        return $vendor;
    }

    public function updateAvatar($request): Vendor
    {
        $vendor = Vendor::find($request->user('api-vendor')->id);
        
        Storage::disk('public')->put('/vendors/avatars/' . $vendor->id, $request->file('avatar'));
        
        Storage::disk('public')->delete('/vendors/avatars/' . $vendor->id . '/' . $vendor->avatar);
        
        $vendor->update([
            'avatar' => $request->file('avatar')->hashName()
        ]);

        return $vendor->fresh();
    }
}
