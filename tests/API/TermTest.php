<?php

namespace Tests\Feature;

use KRLX\Term;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TermTest extends TestCase
{

    public $term;

    public function setUp()
    {
        parent::setUp();
        $this->term = factory(Term::class)->create();
    }

    /**
     * Test that terms can be created.
     *
     * @return void
     */
    public function testTermsCanBeCreated()
    {
        $request = $this->json('POST', '/api/v1/terms', [
            'id' => date('Y').'-SP',
            'on_air' => date('Y').'-04-01 21:00:00',
            'off_air' => date('Y').'-06-01 21:00:00'
        ]);

        $request->assertStatus(201);
    }

    /**
     * Test that an individual term can be queried.
     *
     * @return void
     */
    public function testQueryingIndividualTerm()
    {
        $request = $this->json('GET', "/api/v1/terms/{$this->term->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $this->term->id,
                    'on_air' => $this->term->on_air,
                    'off_air' => $this->term->off_air,
                ])
                ->assertJsonMissing(['created_at', 'updated_at']);
    }
}
