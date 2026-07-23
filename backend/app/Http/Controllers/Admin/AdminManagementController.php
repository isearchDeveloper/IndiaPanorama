<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AdminPermissions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    private function allPermissionNames(): array
    {
        return AdminPermissions::allNames();
    }

    private function permissionsForView(): \Illuminate\Support\Collection
    {
        return AdminPermissions::forView();
    }

    public function index()
    {
        $admins = User::where('is_admin', true)->latest()->get();
        return view('admin.admin-management.index', compact('admins'));
    }

    public function create()
    {
        $permissions = $this->permissionsForView();
        return view('admin.admin-management.create', compact('permissions'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'permissions'   => 'nullable|array',
            'permissions.*' => ['string', Rule::in($this->allPermissionNames())],
        ]);

        $user = User::create([
            'name'           => $r->name,
            'email'          => $r->email,
            'password'       => $r->password,
            'is_admin'       => true,
            'is_super_admin' => false,
            'is_active'      => true,
        ]);

        $user->syncPermissions($r->input('permissions', []));

        ActivityLog::log('created', 'Admin Management', "Created admin: {$user->name} ({$user->email})");

        return redirect()->route('admin.admin-management.index')
            ->with('success', 'Admin created successfully.');
    }

    public function edit(User $adminUser)
    {
        if ($adminUser->is_super_admin) {
            return redirect()->route('admin.admin-management.index')
                ->with('error', 'Super admin cannot be edited here.');
        }

        $permissions     = $this->permissionsForView();
        $userPermissions = $adminUser->permissionNames();

        return view('admin.admin-management.edit', compact('adminUser', 'permissions', 'userPermissions'));
    }

    public function update(Request $r, User $adminUser)
    {
        if ($adminUser->is_super_admin) {
            return redirect()->route('admin.admin-management.index')
                ->with('error', 'Super admin cannot be edited.');
        }

        $r->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $adminUser->id,
            'password'      => 'nullable|string|min:8|confirmed',
            'permissions'   => 'nullable|array',
            'permissions.*' => ['string', Rule::in($this->allPermissionNames())],
        ]);

        $data = ['name' => $r->name, 'email' => $r->email];
        if ($r->filled('password')) {
            $data['password'] = $r->password;
        }
        $adminUser->update($data);
        $adminUser->syncPermissions($r->input('permissions', []));

        ActivityLog::log('updated', 'Admin Management', "Updated admin: {$adminUser->name} ({$adminUser->email})");

        return redirect()->route('admin.admin-management.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $adminUser)
    {
        if ($adminUser->is_super_admin) {
            return redirect()->route('admin.admin-management.index')
                ->with('error', 'Super admin cannot be deleted.');
        }

        if ($adminUser->id === auth()->id()) {
            return redirect()->route('admin.admin-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name  = $adminUser->name;
        $email = $adminUser->email;
        $adminUser->delete();

        ActivityLog::log('deleted', 'Admin Management', "Deleted admin: {$name} ({$email})");

        return redirect()->route('admin.admin-management.index')
            ->with('success', 'Admin deleted successfully.');
    }

    public function toggleStatus(User $adminUser)
    {
        if ($adminUser->is_super_admin) {
            return response()->json(['error' => 'Super admin status cannot be changed.'], 422);
        }

        $adminUser->update(['is_active' => !$adminUser->is_active]);

        $status = $adminUser->is_active ? 'activated' : 'deactivated';
        ActivityLog::log('status-changed', 'Admin Management', "Admin {$status}: {$adminUser->name} ({$adminUser->email})");

        return response()->json(['success' => true, 'is_active' => (bool) $adminUser->is_active]);
    }
}
