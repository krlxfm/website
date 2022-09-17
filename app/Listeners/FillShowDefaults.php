<?php

namespace KRLX\Listeners;

use Faker\Factory as Faker;
use Jdenticon\Identicon;
use KRLX\Events\ShowCreating;
use KRLX\Show;

class FillShowDefaults
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ShowCreating  $event
     * @return void
     */
    public function handle(ShowCreating $event)
    {
        $faker = Faker::create();
        $show = $event->show;

        /*
         * Generate a Show ID. To ensure that the ID is unique, we'll repeatedly
         * generate if there's a collision. Note that the probability of an ID
         * collision is 1/36 raised to the power of the ID length, which can be
         * adjusted in the config files. At 6 characters, the probability of a
         * collision on the first try is 1 in 2,176,782,336 -- better than 2^31.
         */
        while ($show->id == null) {
            $string = $faker->regexify('[A-Z0-9]{'.config('defaults.show_id_length', 6).'}');
            $check = Show::find($string);
            if (! $check) {
                $show->id = $string;
            }
        }

        $arrays = ['content', 'scheduling', 'etc', 'tags', 'conflicts', 'preferences', 'classes'];
        foreach ($arrays as $array) {
            $show->{$array} = [];
        }
        $custom = ['content', 'scheduling', 'etc'];
        foreach ($custom as $key) {
            $fields = collect($show->track->{$key})->pluck('db')->all();
            $show->{$key} = array_fill_keys($fields, '');
        }

        $show->description = $show->description ?? '';
        $show->title = $show->title ?? $show->track->name.' Show';

        /*
        $icon = new Identicon;
        $icon->setValue($show->id);
        $icon->setSize(300);
        $show->image = $icon->getImageDataUri();
        */
        $show->image = 'https://www.krlx.org';

        $show->special_times = array_fill_keys(array_keys(config('defaults.special_times')), 'm');
    }
}
