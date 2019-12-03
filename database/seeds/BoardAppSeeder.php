<?php

use Illuminate\Database\Seeder;
use KRLX\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BoardAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dates = [['date' => date('Y').'-12-01', 'start' => '08:00', 'end' => '17:00'], ['date' => date('Y').'-12-02', 'start' => '08:00', 'end' => '12:00']];
        $questions = ['What is radio?', 'No seriously, please explain what radio is.'];

        Config::create(['name' => 'interview options', 'value' => json_encode($dates)]);
        Config::create(['name' => 'common questions', 'value' => json_encode($questions)]);

        Permission::create(['name' => 'apply for board seats']);
        Permission::create(['name' => 'apply for Station Manager']);
        Permission::create(['name' => 'review board applications']);
        Permission::create(['name' => 'configure board applications']);

        $board = Role::where('name', 'board')->first();
        $board->givePermissionTo([
            'apply for board seats',
            'apply for Station Manager',
            'review board applications',
        ]);
    }
}
