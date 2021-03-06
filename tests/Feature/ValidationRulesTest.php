<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Track;
use Tests\AuthenticatedTestCase;

class ValidationRulesTest extends AuthenticatedTestCase
{
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->actingAs($this->board, 'api');
    }

    /**
     * Test the "validation rule" validation rule, which ensures that custom
     * fields on tracks are created with valid validation rules.
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
                ['title' => 'Sponsor', 'db' => 'sponsor', 'type' => 'shorttext', 'helptext' => null, 'rules' => []],
            ],
        ];

        foreach ($good_rules as $rule) {
            array_set($data, 'content.0.rules', [$rule]);
            $request = $this->session->json('PATCH', "/api/v1/tracks/{$track->id}", $data);
            $this->assertEquals(200, $request->status(), "Did not receive HTTP 200 on rule $rule.");
        }

        foreach ($bad_rules as $rule) {
            array_set($data, 'content.0.rules', [$rule]);
            $request = $this->session->json('PATCH', "/api/v1/tracks/{$track->id}", $data);
            $this->assertEquals(422, $request->status(), "Did not receive HTTP 422 on rule $rule.");
        }
    }

    /**
     * Test the "Profanity" validation rule: Does a string contain bad words?
     *
     * @return void
     */
    public function testProfanityRule()
    {
        $show = factory(Show::class)->create();

        $partial = config('defaults.banned_words.partial')[0];
        $full = config('defaults.banned_words.full')[0];
        $bad_words = [
            $partial,
            $partial.'hole',
            str_plural($partial),
            $full,
            str_plural($full),
            'F***',
            'F**k',
            'sh*t',
            'sh1t',
            'F@$#',
        ];
        $good_words = [
            'prefix'.$full,
            'hole',
            'pals!!!',
            'title',
        ];
        foreach ($good_words as $word) {
            $request = $this->session->json('PATCH', "/api/v1/shows/{$show->id}", ['title' => $word]);
            $this->assertEquals(200, $request->status(), "Did not receive HTTP 200 with word $word.");
        }
        foreach ($bad_words as $word) {
            $request = $this->session->json('PATCH', "/api/v1/shows/{$show->id}", ['title' => $word]);
            $this->assertEquals(422, $request->status(), "Did not receive HTTP 422 with word $word.");
            $this->assertNotEquals('The word  can\'t appear in the title', array_get($request->json(), 'errors.title.0'));
        }
    }
}
