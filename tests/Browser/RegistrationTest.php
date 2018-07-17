<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Login\Login;
use Tests\Browser\Pages\Login\Registration;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test that the registration page redirects users to /login on the first
     * visit (at least, if the session is clean).
     *
     * @return void
     */
    public function testRegisterRedirectsOnFirstVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Test that the Registration page is accessible after entering an email
     * address that doesn't exist within the system.
     *
     * @return void
     */
    public function testRegistrationAccessibleAfterEnteringEmail()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Login)
                     ->type('email', 'test@gmail.com')
                     ->check('@login-terms')
                     ->press('Continue')
                     ->on(new Registration);
        });
    }
}
