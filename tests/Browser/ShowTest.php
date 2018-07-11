<?php

namespace Tests\Browser;

use KRLX\Show;
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
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit(new CreatePage)
                    ->assertSee($this->track->name)
                    ->click("@track-{$this->track->id}")
                    ->waitFor("@show-title")
                    ->assertPresent("@term")
                    ->assertMissing("@term-selector");
        });
    }
}
