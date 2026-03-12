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
            'view dashboard',
            'view pages',
            'create pages',
            'edit pages',
            'publish pages',
            'delete pages',
            'view posts',
            'create posts',
            'edit posts',
            'publish posts',
            'delete posts',
            'manage categories',
            'manage tags',
            'view enquiries',
            'manage enquiries',
            'manage menus',
            'manage settings',
            'manage media',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(
            collect($permissions)->reject(fn ($p) => in_array($p, ['manage users', 'manage roles']))->all()
        );

        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo([
            'view dashboard',
            'view pages',
            'create pages',
            'edit pages',
            'view posts',
            'create posts',
            'edit posts',
            'manage categories',
            'manage tags',
            'manage media',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view dashboard',
            'view pages',
            'view posts',
        ]);
    }
}
