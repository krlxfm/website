<?php

use Illuminate\Database\Seeder;
use KRLX\Config;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::create(['name' => 'active term', 'value' => null]);
        Config::create(['name' => 'landing photo', 'value' => '/img/default.jpg']);
    }
}
