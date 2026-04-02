<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUser\StoreAdminRequest;
use App\Http\Requests\Admin\AdminUser\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = Admin::with('roles')->orderBy('name')->get();

        return view('admins.index', compact('admins'));
    }

    public function create(): View
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();

        return view('admins.create', compact('roles'));
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $role = Role::findById($request->role_id, 'admin');

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role_id'  => $role->id,
        ]);

        $admin->syncRoles([$role]);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin): View
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        $currentRole = $admin->roles->first();

        return view('admins.edit', compact('admin', 'roles', 'currentRole'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $role = Role::findById($request->role_id, 'admin');

        $admin->update(array_filter([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role_id'  => $role->id,
        ], fn($v) => $v !== null));

        $admin->syncRoles([$role]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        abort_if($admin->id === auth('admin')->id(), 403, 'Cannot delete your own account.');

        $admin->delete();

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }
}
