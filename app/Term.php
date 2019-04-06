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
        'id', 'on_air', 'off_air', 'applications_close', 'boosted',
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
        'track_managers' => 'array',
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
        'applications_close',
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
        $shows = $this->shows()->with('track', 'hosts')->get()->filter(function ($show) use ($weekly) {
            return $show->track->weekly == $weekly and ($weekly ? ($show->track->order > 0) : ($show->track->order == 0));
        })->map(function ($show) {
            return [
                (! $show->board_boost),
                $show->track->order,
                ($show->priority->year >= 1000),
                (2000 - $show->priority->terms),
                $show->priority->year,
                (! $show->submitted),
                $show->updated_at,
                $show->id,
            ];
        })->sort(function ($a, $b) {
            return $this->sortShowsByPriority($a, $b);
        });
    }

    /**
     * Function to sort shows in priority order.
     *
     * @param  array  $show_a
     * @param  array  $show_b
     * @return int
     */
    protected function sortShowsByPriority(array $show_a, array $show_b)
    {
        for ($i = 0; $i < count($show_a); $i++) {
            if (($show_a[$i] <=> $show_b[$i]) !== 0) {
                return $show_a[$i] <=> $show_b[$i];
            }
        }
    }
}
