<?php

namespace KRLX;

use KRLX\Events\TrackCreating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description',
    ];

    /**
     * The events that should be automatically dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => TrackCreating::class,
    ];

    /**
     * The attributes that should be hidden from API responses.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that should be type-cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'scheduling' => 'array',
        'etc' => 'array',
        'active' => 'boolean',
        'boostable' => 'boolean',
        'clonable' => 'boolean',
        'allows_images' => 'boolean',
        'can_fall_back' => 'boolean',
        'taggable' => 'boolean',
        'awards_xp' => 'boolean',
        'allows_direct_add' => 'boolean',
        'joinable' => 'boolean',
        'weekly' => 'boolean',
    ];

    /**
     * Generate "dummy" shows (used for calculating next shows) for active,
     * non-recurring tracks.
     *
     * @return Array<KRLX\Show>
     */
    public static function dummyShows()
    {
        $results = [];

        foreach (self::where([['active', true], ['weekly', false]])->get() as $track) {
            if (! $track->start_day or ! $track->start_time or ! $track->end_time) {
                continue;
            }

            $dummy_show = new Show;
            $dummy_show->title = $track->name;
            $dummy_show->id = "TRACK-{$track->id}";
            $dummy_show->day = $track->start_day;
            $dummy_show->start = $track->start_time;
            $dummy_show->end = $track->end_time;

            $dummy_show->track_id = $track->id;

            $results[] = $dummy_show;
        }

        return $results;
    }
}
