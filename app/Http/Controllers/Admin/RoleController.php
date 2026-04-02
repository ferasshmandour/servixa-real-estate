<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->with('permissions')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => explode('-', $p->name, 2)[1] ?? $p->name);

        return view('roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::where('guard_name', 'admin')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => explode('-', $p->name, 2)[1] ?? $p->name);

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'super-admin', 403, 'Cannot modify the super-admin role.');

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === 'super-admin', 403, 'Cannot delete the super-admin role.');
        abort_if($role->name === 'admin', 403, 'Cannot delete the default admin role.');

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
