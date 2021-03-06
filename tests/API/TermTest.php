<?php

namespace Tests\API;

use KRLX\Term;
use Tests\AuthenticatedTestCase;

class TermTest extends AuthenticatedTestCase
{
    /**
     * Test that terms can be created.
     *
     * @return void
     */
    public function testTermsCanBeCreated()
    {
        $request = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/terms', [
            'id' => date('Y').'-SP',
            'on_air' => date('Y').'-04-01 21:00:00',
            'off_air' => date('Y').'-06-01 21:00:00',
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
        $request = $this->actingAs($this->board, 'api')->json('GET', "/api/v1/terms/{$this->term->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $this->term->id,
                    'on_air' => $this->term->on_air,
                    'off_air' => $this->term->off_air,
                ])
                ->assertJsonMissing(['created_at', 'updated_at']);
    }

    /**
     * Test that deleting a term counts as a hard delete.
     *
     * @return void
     */
    public function testTermsDeleteHard()
    {
        $request = $this->actingAs($this->board, 'api')->json('DELETE', "/api/v1/terms/{$this->term->id}");
        $request->assertStatus(204);

        $this->assertNull(Term::find($this->term->id));
    }

    /**
     * Test that all terms can be queried.
     *
     * @return void
     */
    public function testQueryingAllTerms()
    {
        $secondTerm = factory(Term::class)->create();
        $deletedTerm = factory(Term::class)->create();
        $deletedTerm->delete();
        $request = $this->actingAs($this->board, 'api')->json('GET', '/api/v1/terms');

        $request->assertOk()
                ->assertJsonFragment(['id' => $this->term->id])
                ->assertJsonFragment(['id' => $secondTerm->id])
                ->assertJsonMissing(['id' => $deletedTerm->id]);
    }

    /**
     * Test that PATCH requests ONLY update the requested data.
     *
     * @return void
     */
    public function testPatchOnlyUpdatesRequestedData()
    {
        $off_air = $this->term->off_air;
        $request = $this->actingAs($this->board, 'api')->json('PATCH', "/api/v1/terms/{$this->term->id}", [
            'status' => 'active',
        ]);

        $request->assertOk()
                ->assertJson([
                    'status' => 'active',
                    'off_air' => $off_air,
                ]);
    }

    /**
     * Test that PUT requests FAIL if data is missing.
     *
     * @return void
     */
    public function testPutFailsWithMissingAttribute()
    {
        $request = $this->actingAs($this->board, 'api')->json('PUT', "/api/v1/terms/{$this->term->id}", [
            'status' => 'closed',
        ]);

        $request->assertStatus(422);
        $this->assertNotEquals('closed', Term::find($this->term->id)->status);
    }
}
