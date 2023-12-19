<?php

namespace App\Http\Controllers;

use App\Contracts\AdminContract;
use App\Contracts\AdminControllerInterface;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AdminContract $adminContract)
    {
        $admins = $adminContract->buildQuery(
            Admin::query()
        )->paginate(10);

        return AdminResource::collection($admins);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return $this->apiResponse(
            data: AdminResource::make($admin)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminControllerInterface $adminControllerInterface, UpdateAdminRequest $request, Admin $admin)
    {
        $admin = $adminControllerInterface->updateProfile($request, $admin);

        return $this->apiResponse(
            data: AdminResource::make($admin),
            code: 201
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        if (! (Gate::allows('admin-can-delete-himself', $admin) || auth('api-admin')->user()->hasPermission('delete_admins'))) {
            throw new HttpResponseException(
                $this->apiResponse(
                    code: 403,
                    msg: "This admin does not have the right to delete this admin"
                )
            );
        }

        $admin->forceDelete();

        return $this->apiResponse(
            msg: "Admin {$admin->username} got deleted successfully"
        );
    }
}