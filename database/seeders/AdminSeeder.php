<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'super-admin')->where('guard_name', 'admin')->first();

        $admin = Admin::firstOrCreate(
            ['email' => 'admin@servixa.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@servixa.com',
                'password' => 'password',
                'role_id'  => $role?->id,
            ]
        );

        if ($role) {
            $admin->syncRoles([$role]);
            $admin->update(['role_id' => $role->id]);
        }
    }
}
