<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVendorAvatarRequest;
use App\Http\Requests\UpdateVendorPassword;
use App\Http\Requests\UpdateVendorPasswordRequest;
use App\Http\Requests\UpdateVendorProfileRequest;
use App\Http\Resources\VendorResource;
use App\Services\VendorProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorProfileController extends Controller
{
    public function __construct(private VendorProfileService $vendorProfileService)
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
        $vendor = $this->vendorProfileService->updateVendorInfo($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }

    public function updatePassword(UpdateVendorPasswordRequest $request): JsonResponse
    {
        $vendor = $this->vendorProfileService->updatePassword($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }

    public function updateAvatar(UpdateVendorAvatarRequest $request): JsonResponse
    {
        $vendor = $this->vendorProfileService->updateAvatar($request);

        return $this->apiResponse(
            data: VendorResource::make($vendor),
            code: JsonResponse::HTTP_CREATED
        );
    }
}
