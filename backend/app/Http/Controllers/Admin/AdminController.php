<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('is_admin', true)
            ->where('is_super_admin', false)
            ->with(['roles.permissions'])
            ->latest()
            ->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view('admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|exists:roles,name',
        ]);

        $admin = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'is_admin'       => true,
            'is_super_admin' => false,
            'status'         => $request->boolean('status', true),
        ]);

        if ($request->filled('role')) {
            $admin->assignRole($request->role);
        }

        ActivityLog::log('created', 'Admin Management', "Created admin: {$admin->email}");

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function edit(User $admin)
    {
        abort_if($admin->is_super_admin, 403);

        $roles     = Role::where('name', '!=', 'super-admin')->get();
        $adminRole = $admin->roles->first()?->name;

        return view('admin.admins.edit', compact('admin', 'roles', 'adminRole'));
    }

    public function update(Request $request, User $admin)
    {
        abort_if($admin->is_super_admin, 403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8',
            'role'     => 'nullable|exists:roles,name',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => $request->boolean('status', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        $admin->syncRoles($request->filled('role') ? [$request->role] : []);

        ActivityLog::log('updated', 'Admin Management', "Updated admin: {$admin->email}");

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $admin)
    {
        abort_if($admin->is_super_admin, 403);
        abort_if($admin->id === auth()->id(), 403, 'Cannot delete yourself.');

        $email = $admin->email;
        $admin->delete();

        ActivityLog::log('deleted', 'Admin Management', "Deleted admin: {$email}");

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    public function toggleStatus(User $admin)
    {
        abort_if($admin->is_super_admin, 403);
        $admin->update(['status' => !$admin->status]);

        return response()->json(['status' => $admin->status]);
    }
}
