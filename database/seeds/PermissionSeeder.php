<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions.
        Permission::create(['name' => 'see all applications']);
        Permission::create(['name' => 'edit all applications']);
        Permission::create(['name' => 'see all DJs']);
        Permission::create(['name' => 'build schedule']);
        Permission::create(['name' => 'auto-request Zone S']);
        Permission::create(['name' => 'override closed term']);
        Permission::create(['name' => 'override pending term']);
        Permission::create(['name' => 'manage tracks']);
        Permission::create(['name' => 'manage terms']);

        // Create roles and assign permissions.
        $board = Role::create(['name' => 'board']);
        $board->givePermissionTo([
            'edit all applications',
            'see all applications',
            'see all DJs',
            'build schedule',
            'auto-request Zone S',
            'override closed term',
            'override pending term',
            'manage tracks',
            'manage terms',
        ]);

        $tester = Role::create(['name' => 'tester']);
        $tester->givePermissionTo([
            'override pending term',
        ]);
    }
}
