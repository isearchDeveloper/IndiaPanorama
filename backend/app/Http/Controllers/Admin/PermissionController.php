<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(50);
        $modules     = config('permissions.modules');
        return view('admin.permissions.index', compact('permissions', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        ActivityLog::log('created', 'Permission Management', "Created permission: {$permission->name}");

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created.');
    }

    public function destroy(Permission $permission)
    {
        $name = $permission->name;
        $permission->delete();
        ActivityLog::log('deleted', 'Permission Management', "Deleted permission: {$name}");

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted.');
    }

    public function sync()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = config('permissions.modules');
        $count   = 0;
        foreach ($modules as $module) {
            foreach ($module['actions'] as $action) {
                $name = $action . '-' . $module['key'];
                if (!Permission::where('name', $name)->exists()) {
                    Permission::create(['name' => $name, 'guard_name' => 'web']);
                    $count++;
                }
            }
        }

        ActivityLog::log('updated', 'Permission Management', "Synced {$count} new permissions from config.");

        return redirect()->route('admin.permissions.index')
            ->with('success', "Synced {$count} new permissions from config.");
    }
}
