<?php

namespace Tests\Browser;

use KRLX\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Login\Login;
use Tests\Browser\Pages\Login\Password;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

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
                    ->press('Sign in');
        });
    }
}
