<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class OnboardTest extends AuthenticatedTestCase
{
    use WithFaker;

    /**
     * Test that onboarding is mandatory for Carleton accounts, but not guests.
     *
     * @return void
     */
    public function testCarletonOnboardingMandatoryForNewCarls()
    {
        $this->assertNull($this->new_carl->phone_number);

        $request = $this->actingAs($this->new_carl)->get('/home');
        $carl_request = $this->actingAs($this->carleton)->get('/home');
        $guest_request = $this->actingAs($this->guest)->get('/home');

        $request->assertRedirect('/welcome');
        $carl_request->assertOk();
        $guest_request->assertOk();
    }

    /**
     * Test that users who don't need onboarding are redirected to the
     * profile screen, but those who DO need onboarding are instead presented
     * with the onboarding view.
     *
     * @return void
     */
    public function testUnnecessaryOnboardingIsPrevented()
    {
        $req_new_carl = $this->actingAs($this->new_carl)->get('/welcome');
        $req_carleton = $this->actingAs($this->carleton)->get('/welcome');
        $req_guest = $this->actingAs($this->guest)->get('/welcome');

        $req_new_carl->assertOk()
                     ->assertViewIs('legal.onboard');
        $req_carleton->assertRedirect('/profile');
        $req_guest->assertRedirect('/profile');
    }

    /**
     * Test that onboarding requests for students can be saved.
     *
     * @return void
     */
    public function testOnboardingSavesForStudents()
    {
        $phone = $this->faker()->regexify('507-222-[0-9]{4}');
        $req_carleton = $this->actingAs($this->new_carl)->post('/welcome', [
            'first_name' => $this->carleton->first_name,
            'name' => $this->carleton->name,
            'phone_number' => $phone,
            'status' => 'student',
            'year' => $this->carleton->year,
        ]);

        $user = User::find($this->new_carl->id);

        $this->assertEquals($phone, $user->phone_number);
        $req_carleton->assertRedirect('/home')
                     ->assertSessionHas('status', 'Your account has been activated!');
    }

    /**
     * Test that onboarding saves for faculty and staff.
     *
     * @return void
     */
    public function testOnboardingSavesForFacultyAndStaff()
    {
        $options = [1 => 'faculty', 2 => 'staff'];
        foreach ($options as $year => $value) {
            $phone = $this->faker()->regexify('507-222-[0-9]{4}');
            $req_carleton = $this->actingAs($this->carleton)->post('/welcome', [
                'first_name' => $this->carleton->first_name,
                'name' => $this->carleton->name,
                'phone_number' => $phone,
                'status' => $value,
            ]);

            $user = User::find($this->carleton->id);

            $this->assertEquals($year, $user->year);
            $this->assertEquals($phone, $user->phone_number);
            $this->assertContains(ucwords($value), $user->priority->html());
            $req_carleton->assertRedirect('/home');
        }
    }
}
