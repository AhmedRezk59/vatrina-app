<?php

namespace App\Contracts;

use App\Models\Admin;
use Illuminate\Http\Request;

interface AdminControllerInterface
{
    public function updateProfile(Request $request , Admin $admin):Admin;
}