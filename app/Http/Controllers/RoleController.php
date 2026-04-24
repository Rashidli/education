<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles' => Role::with('permissions')->latest()->get(),
            'permissions' => Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]),
            'permissionLabels' => RolesAndPermissionsSeeder::PERMISSIONS,
        ]);
    }

    public function create()
    {
        return view('roles.create', [
            'role' => new Role(),
            'permissions' => Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]),
            'permissionLabels' => RolesAndPermissionsSeeder::PERMISSIONS,
            'rolePermissions' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $request->input('name'), 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Rol əlavə olundu.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0]),
            'permissionLabels' => RolesAndPermissionsSeeder::PERMISSIONS,
            'rolePermissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === User::ROLE_SUPER_ADMIN) {
            return back()->with('error', 'Super Admin rolu dəyişdirilə bilməz.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Rol yeniləndi.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_MANAGER], true)) {
            return back()->with('error', 'Sistem rolları silinə bilməz.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol silindi.');
    }

    public function show(Role $role)
    {
        return redirect()->route('roles.edit', $role);
    }
}
