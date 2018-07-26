<?php

namespace Tests\Browser;

use KRLX\Show;
use KRLX\Track;
use KRLX\Term;
use KRLX\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Shows\Create as CreatePage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public $track;
    public $term;
    public $show;
    public $user;

    public function setUp()
    {
        parent::setUp();

        $this->show = factory(Show::class)->create();
        $this->track = $this->show->track;
        $this->term = $this->show->term;
        $this->user = factory(User::class)->create();
        $this->user->shows()->attach($this->show, ['accepted' => true]);
    }

    /**
     * Test that, when clicking on an active track name and only one term is
     * accepting applications, the title prompt appears and the term is not
     * user selectable.
     *
     * @return void
     */
    public function testShowCreation()
    {
        $this->browse(function (Browser $browser) {
            $this->assertCount(1, Term::all());

            $browser->loginAs($this->user)
                    ->visit(new CreatePage)
                    ->assertSee($this->track->name)
                    ->click("@track-{$this->track->id}")
                    ->waitFor('@show-title')
                    ->assertPresent('@term')
                    ->assertMissing('@term-selector')
                    ->type('title', 'Example Show')
                    ->click('@create-show');

            $show = Show::where('title', 'Example Show')->first();
            $browser->assertRouteIs('shows.hosts', $show->id);
        });
    }

    /**
     * Test that validation only runs on the affected field.
     *
     * @return void
     */
    public function testValidationOnlyRunsOnCurrentField()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/content")
                    ->type('title', 'A')
                    ->click('#description')
                    ->pause(500)
                    ->assertSee('The title must be at least')
                    ->assertDontSee('The description must be at least')
                    ->type('title', 'Amazing Show Title')
                    ->click('#description')
                    ->pause(500)
                    ->assertDontSee('The title must be at least')
                    ->assertSee('Changes saved');
        });
        $show = Show::find($this->show->id);
        $this->assertEquals('Amazing Show Title', $show->title);
    }

    /**
     * Test that the "content" prefix doesn't appear in error messages.
     *
     * @return void
     */
    public function testCustomFieldsDontIncludeParent()
    {
        $track = factory(Track::class)->create([
            'active' => true,
            'content' => [
                ['name' => 'Sponsor', 'db' => 'sponsor', 'type' => 'shorttext', 'helptext' => null, 'rules' => ['required', 'min:3']]
            ]
        ]);
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $track->id
        ]);
        $this->user->shows()->attach($show, ['accepted' => true]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/content")
                    ->type('content.sponsor', 'A')
                    ->click('#title')
                    ->pause(500)
                    ->assertSee('The sponsor must be at least')
                    ->assertDontSee('The content.sponsor must be at least');
    }
}
