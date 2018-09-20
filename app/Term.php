<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    /**
     * Attribute overrides to allow for non-integer primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Attribute overrides to allow for non-integer primary key.
     *
     * @var bool
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'on_air', 'off_air', 'boosted',
    ];

    /**
     * The attributes that should be hidden from API calls.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be mutated to non-date things.
     *
     * @var array
     */
    protected $casts = [
        'boosted' => 'boolean',
    ];

    /**
     * The attributes that should be added to arrays.
     *
     * @var array
     */
    protected $appends = [
        'name',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'on_air',
        'off_air',
    ];

    /**
     * Computes the "long form" name of a term.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $components = explode('-', $this->id);
        if (array_key_exists($components[1], config('terms'))) {
            return config('terms')[$components[1]].' '.$components[0];
        } else {
            return str_replace('_', ' ', title_case($components[1])).' '.$components[0];
        }
    }

    /**
     * Get the shows attached to this term.
     *
     * @return Eloquent\Collection<KRLX\Show>
     */
    public function shows()
    {
        return $this->hasMany('KRLX\Show');
    }

    /**
     * Get the year attached to the term.
     *
     * @return int
     */
    public function getYearAttribute()
    {
        return intval(explode('-', $this->id)[0]);
    }

    /**
     * Returns the list of shows as a part of a term, sorted in priority order.
     *
     * @param  bool  $weekly
     * @return Eloquent\Collection<Show>
     */
    public function showsInPriorityOrder(bool $weekly)
    {
        $shows = $this->shows()->with('track', 'hosts')->get();
        return $shows->filter(function($show) use ($weekly) {
            return ($show->track->weekly == $weekly and ($weekly ? ($show->track->order > 0) : ($show->track->order == 0)));
        })->sort(function ($a, $b) {
            return $this->sortShowsByPriority($a, $b);
        });
    }

    /**
     * Function to sort shows in priority order.
     *
     * @param  Show  $show_a
     * @param  Show  $show_b
     * @return int
     */
    protected function sortShowsByPriority(Show $show_a, Show $show_b)
    {
        $priority_a = $show_a->priority;
        $priority_b = $show_b->priority;

        $boost_diff = $show_b->board_boost <=> $show_a->board_boost;
        $track_diff = $show_a->track->order <=> $show_b->track->order;
        $faculty_diff = ($priority_b->year < 1000) <=> ($priority_a->year < 1000);
        $zone_diff = $priority_b->terms <=> $priority_a->terms;
        $year_diff = $priority_a->year <=> $priority_b->year;
        $completed_diff = $show_b->submitted <=> $show_a->submitted;
        $updated_at_diff = $show_a->updated_at <=> $show_b->updated_at;
        $id_diff = $show_a->id <=> $show_b->id;

        $diffs = [$boost_diff, $track_diff, $faculty_diff, $zone_diff, $year_diff, $completed_diff, $updated_at_diff, $id_diff];

        foreach ($diffs as $diff) {
            if ($diff != 0) {
                return $diff;
            }
        }
    }
}
