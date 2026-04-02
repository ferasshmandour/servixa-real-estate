<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-business-accounts',
            'manage-business-accounts',
            'view-services',
            'manage-services',
            'view-categories',
            'manage-categories',
            'view-cities',
            'manage-cities',
            'view-sliders',
            'manage-sliders',
            'view-reports',
            'manage-reports',
            'manage-roles',
            'manage-admins',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $superAdmin->syncPermissions(Permission::where('guard_name', 'admin')->get());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin->syncPermissions([
            'view-business-accounts',
            'manage-business-accounts',
            'view-services',
            'manage-services',
            'view-categories',
            'manage-categories',
            'view-cities',
            'manage-cities',
            'view-sliders',
            'view-reports',
        ]);
    }
}
