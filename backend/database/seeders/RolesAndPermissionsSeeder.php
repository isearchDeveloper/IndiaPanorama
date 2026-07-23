<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = config('permissions.modules');

        // Create all permissions
        $allPermissions = [];
        foreach ($modules as $module) {
            foreach ($module['actions'] as $action) {
                $name = $action . '-' . $module['key'];
                $permission = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $allPermissions[] = $permission->name;
            }
        }

        // Create Super Admin role and give all permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        // Create Content Manager role (packages, hotels, trains, etc.)
        $contentRole = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'web']);
        $contentPermissions = [];
        $contentModules = ['packages', 'special-packages', 'deal-packages', 'categories', 'themes',
                           'festivals', 'countries', 'locations', 'regions', 'cities', 'hotels',
                           'trains', 'cars', 'buses', 'cms-pages', 'news', 'events', 'go-explore',
                           'banners', 'promotional-ads', 'offers', 'summer', 'sitemap'];
        foreach ($allPermissions as $perm) {
            foreach ($contentModules as $mod) {
                if (str_ends_with($perm, '-' . $mod)) {
                    $contentPermissions[] = $perm;
                    break;
                }
            }
        }
        $contentRole->syncPermissions($contentPermissions);

        // Create Enquiry Manager role
        $enquiryRole = Role::firstOrCreate(['name' => 'enquiry-manager', 'guard_name' => 'web']);
        $enquiryRole->syncPermissions(
            array_filter($allPermissions, fn($p) => str_ends_with($p, '-enquiries') || str_ends_with($p, '-reviews'))
        );

        // Create/update Super Admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@indianpanorama.in'],
            [
                'name'           => 'Super Admin',
                'password'       => Hash::make('Admin@1234#CRM'),
                'is_admin'       => true,
                'is_super_admin' => true,
                'status'         => true,
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);
        $superAdmin->syncPermissions($allPermissions);

        $this->command->info('✅ Roles & Permissions seeded successfully.');
        $this->command->info('   Super Admin: superadmin@indianpanorama.in | Password: Admin@1234#CRM');
    }
}
