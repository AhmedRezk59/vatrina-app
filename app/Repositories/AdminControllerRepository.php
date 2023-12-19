<?php

namespace App\Repositories;

use App\Contracts\AdminControllerInterface;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

class AdminControllerRepository implements AdminControllerInterface
{
    public function updateProfile(Request $request, Admin $admin): Admin
    {
        $data = $request->validated();
        
        if (isset($request->avatar)) {
            Storage::disk('public')->delete($admin->avatar);

            $path = Storage::disk('public')->put('/admins/avatars/', $request->file('avatar'));
            $data['avatar'] = $path;
        }

        $admin->update($data);

        return $admin->fresh();
    }
}