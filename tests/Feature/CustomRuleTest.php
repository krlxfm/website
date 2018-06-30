<?php

namespace Tests\Feature;

use KRLX\Track;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomRuleTest extends TestCase
{
    /**
     * Test the "ValidationRule" validation rule: Can a string be used as a
     * validation rule?
     *
     * @return void
     */
    public function testValidationRuleValidationRule()
    {
        $track = factory(Track::class)->create();
        $good_rules = ['required', 'min:0', 'max:200', 'profanity'];
        $bad_rules = ['potato', 'min:', 'max:a', 'english:3', 'min:-1'];
        $data = [
            'content' => [
                ['title' => 'Sponsor', 'db' => 'sponsor', 'type' => 'shorttext', 'helptext' => null, 'rules' => []]
            ]
        ];

        foreach($good_rules as $rule) {
            array_set($data, 'content.0.rules', [$rule]);
            $request = $this->json('PATCH', "/api/v1/tracks/{$track->id}", $data);
            $this->assertEquals(200, $request->status(), "Did not receive HTTP 200 on rule $rule.");
        }

        foreach($bad_rules as $rule) {
            array_set($data, 'content.0.rules', [$rule]);
            $request = $this->json('PATCH', "/api/v1/tracks/{$track->id}", $data);
            $this->assertEquals(422, $request->status(), "Did not receive HTTP 422 on rule $rule.");
        }
    }
}
