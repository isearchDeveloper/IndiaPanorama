<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->where('name', '!=', 'super-admin')->paginate(20);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $modules = config('permissions.modules');
        return view('admin.roles.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $submitted   = $request->input('permissions', []);
        $validPerms  = Permission::whereIn('name', $submitted)->pluck('name');
        $role->syncPermissions($validPerms);

        ActivityLog::log('created', 'Role Management', "Created role: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'super-admin', 403);
        $modules         = config('permissions.modules');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'modules', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'super-admin', 403);

        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update(['name' => $request->name]);
        $submitted   = $request->input('permissions', []);
        $validPerms  = Permission::whereIn('name', $submitted)->pluck('name');
        $role->syncPermissions($validPerms);

        ActivityLog::log('updated', 'Role Management', "Updated role: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        abort_if($role->name === 'super-admin', 403);
        $name = $role->name;
        $role->delete();

        ActivityLog::log('deleted', 'Role Management', "Deleted role: {$name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
