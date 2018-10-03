<?php

namespace Tests\Feature;

use KRLX\User;
use Tests\AuthenticatedTestCase;

class OnboardTest extends AuthenticatedTestCase
{
    /**
     * Test that onboarding is mandatory for Carleton accounts.
     *
     * @return void
     */
    public function testCarletonOnboardingMandatory()
    {
        $user = factory(User::class)->states('carleton_new')->create();

        $this->assertNull($user->phone_number);

        $request = $this->actingAs($user)->get('/home');
        $request->assertRedirect('/welcome');
    }

    /**
     * Test that users who don't need onboarding are redirected to the
     * profile screen.
     *
     * @return void
     */
    public function testUnnecessaryOnboardingIsPrevented()
    {
        $req_carleton = $this->actingAs($this->carleton)->get('/welcome');
        $req_guest = $this->actingAs($this->guest)->get('/welcome');

        $req_carleton->assertRedirect('/profile');
        $req_guest->assertRedirect('/profile');
    }
}
