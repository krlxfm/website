<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BoardAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'apply for board seats']);
        Permission::create(['name' => 'apply for Station Manager']);
        Permission::create(['name' => 'review board applications']);

        $board = Role::where('name', 'board')->first();
        $board->givePermissionTo([
            'apply for board seats',
            'apply for Station Manager',
            'review board applications',
        ]);
    }
}
