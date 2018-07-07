<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    /**
     * Assert that shows can be created via the API.
     *
     * @return void
     */
    public function testShowsCanBeCreated()
    {
        $request = $this->json('POST', '/api/v1/shows', [
            'title' => 'Gray Duck'
        ]);

        $request->assertStatus(201);
    }
}
