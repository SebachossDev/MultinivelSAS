<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $promotorRole = Role::create(['name' => 'Promotor']);
        $vendedorRole = Role::create(['name' => 'Vendedor']);

        
        $adminRole->givePermissionTo('Admin');
        $promotorRole->givePermissionTo('Promotor');
        $vendedorRole->givePermissionTo('Vendedor');
    }
}