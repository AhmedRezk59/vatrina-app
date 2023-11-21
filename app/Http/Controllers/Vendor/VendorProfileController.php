<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\VendorProfileContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVendorAvatarRequest;
use App\Http\Requests\UpdateVendorPassword;
use App\Http\Requests\UpdateVendorPasswordRequest;
use App\Http\Requests\UpdateVendorProfileRequest;
use App\Http\Resources\VendorResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorProfileController extends Controller
{
    public function __construct(private VendorProfileContract $vendorProfileRepository)
    {
    }

    public function user(Request $request): JsonResponse
    {
        return $this->apiResponse(
            data: VendorResource::make($request->user('api-vendor'))
        );
    }

    public function updateInfo(UpdateVendorProfileRequest $request): JsonResponse
    {
        $vendor = $this->vendorProfileRepository->updateVendorInfo($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }

    public function updatePassword(UpdateVendorPasswordRequest $request): JsonResponse
    {
        $vendor = $this->vendorProfileRepository->updatePassword($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }

    public function updateAvatar(UpdateVendorAvatarRequest $request): JsonResponse
    {
        $vendor = $this->vendorProfileRepository->updateAvatar($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }
}