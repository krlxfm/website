<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use KRLX\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Login\Login;
use Tests\Browser\Pages\Login\Password;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test that visiting /login/password on a clean session redirects you
     * to /login.
     *
     * @return void
     */
    public function testLoginPasswordRedirects()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/password')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Test that visiting /login/carleton on a clean session redirects you
     * to /login.
     *
     * @return void
     */
    public function testLoginCarletonRedirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login/carleton')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Test the email/password login flow for an existing account.
     *
     * @return void
     */
    public function testLogin()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Login)
                    ->assertPresent('@login-terms')
                    ->assertMissing('@login-password')
                    ->type('email', $user->email)
                    ->check('@login-terms')
                    ->press('Continue')
                    ->on(new Password)
                    ->assertSee('Welcome back, '.$user->first_name)
                    ->assertPresent('@login-password')
                    ->assertMissing('@login-terms')
                    ->type('password', 'secret')
                    ->press('Sign in')
                    ->assertPathIs('/home');
        });
    }
}
