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
            'on_air' => date('Y').'-04-01 21:00:00',
            'off_air' => date('Y').'-06-01 21:00:00'
        ]);

        $request->assertStatus(201);
    }
}
