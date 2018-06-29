<?php

namespace Tests\Feature;

use KRLX\Term;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TermTest extends TestCase
{
    /**
     * Test that terms can be created.
     *
     * @return void
     */
    public function testTermsCanBeCreated()
    {
        $request = $this->json('POST', '/api/v1/terms', [
            'id' => date('Y').'-SP',
            'on_air' => Carbon::create(date('Y'), 4, 1, 21),
            'off_air' => Carbon::create(date('Y'), 6, 1, 21)
        ]);

        $request->assertStatus(201);
    }
}
