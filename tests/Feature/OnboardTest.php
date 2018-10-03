<?php

namespace Tests\Feature;

use KRLX\User;
use Tests\AuthenticatedTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class OnboardTest extends AuthenticatedTestCase
{
    use WithFaker;

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

    /**
     * Test that onboarding requests for students can be saved.
     *
     * @return void
     */
    public function testOnboardingSavesForStudents()
    {
        $phone = $this->faker()->regexify('507-222-[0-9]{4}');
        $req_carleton = $this->actingAs($this->carleton)->post('/welcome', [
            'first_name' => $this->carleton->first_name,
            'name' => $this->carleton->name,
            'phone_number' => $phone,
            'status' => 'student',
            'year' => $this->carleton->year,
        ]);

        $user = User::find($this->carleton->id);

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
         foreach($options as $year => $value) {
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
             $this->assertContains(ucwords($value), $user->priority->display());
             $req_carleton->assertRedirect('/home');
         }
     }
}
