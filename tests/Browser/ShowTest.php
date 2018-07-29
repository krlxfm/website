<?php

namespace Tests\Browser;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
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
     * Test that new hosts' names appear in the list as soon as they're added.
     *
     * @return void
     */
    public function testNewHostsAppearInList()
    {
        $this->browse(function (Browser $browser) {
            $this->assertCount(1, Term::all());
            $user = factory(User::class)->create();

            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/hosts")
                    ->click('@add-host')
                    ->waitFor('@participant-add-modal')
                    ->type('search', $user->email)
                    ->waitForText(e($user->name))
                    ->click('[data-email="'.$user->email.'"]')
                    ->waitUntilMissing('@participant-add-modal')
                    ->assertSee('Cancel invitation')
                    ->assertSee(e($user->name));
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
     * Test that the preferred show times box updates the end time to be offset
     * by the preferred show length whenever the start time is changed.
     *
     * @return void
     */
    public function testPreferredEndTimeOffsetToSetLength()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/schedule")
                    ->mouseover('@schedule-standard-return')
                    ->click('@add-preference-button')
                    ->waitFor('@preference-manager-modal')
                    ->select('preference-start', '13:00')
                    ->pause(500)
                    ->assertSelected('preference-end', '14:00');
        });
    }

    /**
     * Test that class selections do, in fact, save.
     *
     * @return void
     */
    public function testClassTimesSave()
    {
        $this->assertEmpty($this->show->classes);
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/schedule")
                    ->mouseover('#classes-6a')
                    ->click('@classes-1a-label')
                    ->waitForText('Changes saved');
        });
        $show = Show::find($this->show->id);
        $this->assertNotEmpty($show->classes);
        $this->assertContains('1a', $show->classes);
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
                ['title' => 'Sponsor', 'db' => 'sponsor', 'type' => 'shorttext', 'helptext' => null, 'rules' => ['required', 'min:3']],
            ],
        ]);
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $track->id,
        ]);
        $this->user->shows()->attach($show, ['accepted' => true]);

        $this->browse(function (Browser $browser) use ($show) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$show->id}/content")
                    ->type('#sponsor', 'A')
                    ->click('#title')
                    ->pause(500)
                    ->assertSee('The sponsor must be at least')
                    ->assertDontSee('The content.sponsor must be at least');
        });
    }

    /**
     * Test that conflicts/preferences without any days selected don't save.
     *
     * @return void
     */
    public function testConflictsAndPreferencesWithoutDaysDontSave()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit("/shows/{$this->show->id}/schedule")
                    ->mouseover('@schedule-standard-return')
                    ->click('@add-conflict-button')
                    ->waitFor('@conflict-manager-modal')
                    ->select('conflict-start', '13:00')
                    ->select('conflict-end', '14:00')
                    ->click('@save-conflict')
                    ->waitUntilMissing('@conflict-manager-modal')
                    ->assertDontSee('1:00 pm - 2:00 pm')

                    ->mouseover('@schedule-standard-return')
                    ->click('@add-preference-button')
                    ->waitFor('@preference-manager-modal')
                    ->select('preference-start', '13:00')
                    ->select('preference-strength', 3)
                    ->click('@save-preference')
                    ->waitUntilMissing('@preference-manager-modal')
                    ->assertDontSee('First Choice');
        });
    }
}
