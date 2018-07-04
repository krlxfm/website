<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Login\Registration;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertPathIs('/login');
        });
    }
}
