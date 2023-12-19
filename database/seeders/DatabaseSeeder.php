<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\Collection;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::factory(1)->create();
        User::factory(10)->create();
        Vendor::factory(10)->create();
        Collection::factory(3)->create();
        Product::factory(30)->create();
        Order::factory(20);
        
        Permission::create(
            [
                'name' => 'ban-vendor',
                'display_name' => 'Ban vendor',
                'description' => 'Admin can ban vendors.',
            ]
        );
        Permission::create(
            [

                'name' => 'delete_admins',
                'display_name' => 'Delete an admin',
                'description' => 'Admin can delete other admins.',
            ]
        );
    }
}