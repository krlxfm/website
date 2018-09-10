<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions.
        Permission::create(['name' => 'see all applications']);
        Permission::create(['name' => 'see all DJs']);
        Permission::create(['name' => 'build schedule']);
        Permission::create(['name' => 'auto-request Zone S']);
        Permission::create(['name' => 'override closed term']);
        Permission::create(['name' => 'override pending term']);

        // Create roles and assign permissions.
        $board = Role::create(['name' => 'board']);
        $board->givePermissionTo([
            'see all applications',
            'see all DJs',
            'build schedule',
            'auto-request Zone S',
            'override closed term',
            'override pending term'
        ]);

        $tester = Role::create(['name' => 'tester']);
        $tester->givePermissionTo([
            'override pending term'
        ]);
    }
}
