<?php

namespace Tests\Feature;

use KRLX\User;
use Tests\AuthenticatedTestCase;

class ProfileTest extends AuthenticatedTestCase
{
    /**
     * Test that all users can access the profile screen.
     *
     * @return void
     */
    public function testAllUsersCanAccessProfile()
    {
        $guest_req = $this->actingAs($this->guest)->get('/profile');
        $carleton_req = $this->actingAs($this->carleton)->get('/profile');

        $guest_req->assertOk()
                  ->assertDontSee('Carleton status');
        $carleton_req->assertOk()
                     ->assertSee('Carleton status');
    }

    /**
     * Test that saving profile changes brings the user back to Home and
     * flashes a "Profile updated" message.
     *
     * @return void
     */
    public function testSavingProfileReturnsUserHome()
    {
        $request = $this->actingAs($this->carleton)->post('/welcome', [
            'source' => 'profile',
            'first_name' => $this->carleton->first_name,
            'name' => $this->carleton->name,
            'phone_number' => $this->carleton->phone_number,
            'status' => 'student',
            'year' => $this->carleton->year,
            'bio' => 'This is a short biography about a test account.',
        ]);

        $request->assertRedirect('/home')
                ->assertSessionHas('status', 'Your profile has been updated!');
    }
}
