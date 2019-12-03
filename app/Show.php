<?php

namespace KRLX;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use KRLX\Events\ShowCreating;

class Show extends Model
{
    use SoftDeletes;

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
        'title', 'term_id', 'track_id', 'source',
    ];

    /**
     * The events that should be dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => ShowCreating::class,
    ];

    /**
     * The attributes that should be type cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'scheduling' => 'array',
        'etc' => 'array',
        'conflicts' => 'array',
        'classes' => 'array',
        'tags' => 'array',
        'preferences' => 'array',
        'special_times' => 'array',
        'submitted' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'priority_code',
        'board_boost',
    ];

    /**
     * Shows have a Track ID that corresponds to a Track.
     *
     * @return Track
     */
    public function track()
    {
        return $this->belongsTo('KRLX\Track');
    }

    /**
     * Shows have a Term ID that corresponds to a Term.
     *
     * @return Term
     */
    public function term()
    {
        return $this->belongsTo('KRLX\Term');
    }

    /**
     * DJs that have accepted invitations to join the show.
     *
     * @return Eloquent\Collection<KRLX\Show>
     */
    public function hosts()
    {
        return $this->belongsToMany(User::class)
                    ->wherePivot('accepted', 1)
                    ->withPivot('accepted')
                    ->as('membership')
                    ->withTimestamps();
    }

    /**
     * DJs that have not yet accepted invitations to join the show, but have
     * been invited.
     *
     * @return Eloquent\Collection<KRLX\Show>
     */
    public function invitees()
    {
        return $this->belongsToMany(User::class)
                    ->wherePivot('accepted', 0)
                    ->withPivot('accepted')
                    ->as('membership')
                    ->withTimestamps();
    }

    /**
     * Returns all boosts applied to this show.
     *
     * @return Eloquent\Collection<KRLX\Boost>
     */
    public function boosts()
    {
        return $this->hasMany('KRLX\Boost');
    }

    /**
     * Determine whether or not the show has a Priority Boost request on it.
     * Participants MUST BE HOSTS in order to qualify!
     *
     * @return bool
     */
    public function getBoostedAttribute()
    {
        $hosts = $this->hosts;
        $boosts = $this->boosts->filter(function ($boost) use ($hosts) {
            return $hosts->contains($boost->user);
        });

        return $boosts->count() > 0;
    }

    /**
     * Returns whether or not the show has any Board Upgrade Certificates on it.
     *
     * @return bool
     */
    public function getBoardBoostAttribute()
    {
        return $this->boosts()->where('type', 'S')->count() > 0;
    }

    /**
     * Generate the show's priority object.
     *
     * @return Priority
     */
    public function getPriorityAttribute($value)
    {
        if ($value) {
            $term_numbers = array_merge(range('J', 'B'), ['A3', 'A2', 'A1', 'A']);
            $terms = array_search($value, $term_numbers);
            $year = $this->term->year - ($this->term->boosted ? 1 : 0);
            $relative_year = $year;
            if ($terms === false) {
                $terms = array_search($value[0], $term_numbers);
                $year += (int) $value[1];
            }

            return new Priority($terms, $year, $relative_year);
        }

        $host_priorities = [];
        foreach ($this->hosts as $host) {
            $host_priorities[] = $host->priorityAsOf($this->term->id);
        }
        $priorities = collect($host_priorities);
        $terms = $priorities->max->terms ?? 0;
        $terms += $this->boosts()->where('type', 'zone')->count();
        $terms += ($this->boosts()->where('type', 'A1')->count() > 0 ? 1000 : 0);

        $year = $priorities->min->year ?? (date('Y') + 4);

        if ($this->track->zone) {
            $terms = array_search($this->track->zone, array_merge(config('defaults.priority.terms'), ['A']));
        }

        if ($this->track->group !== null) {
            $year = $this->track->group + $this->term->year - ($this->term->boosted ? 1 : 0);
        }

        return new Priority($terms, $year, ($this->term->year - ($this->term->boosted ? 1 : 0)));
    }

    /**
     * Shorthand for priority->code so that JavaScript doesn't complain.
     *
     * @return string
     */
    public function getPriorityCodeAttribute()
    {
        return $this->priority->code();
    }

    /**
     * Determine which show is "next" in the schedule.
     *
     * @return null|Show
     */
    public function getNextAttribute()
    {
        if (! $this->day or ! $this->start or ! $this->end) {
            return;
        }
        $track_shows = Track::dummyShows();
        $track_managers = $this->term->track_managers;
        foreach ($track_shows as $dummy_show) {
            $track_id = explode('-', $dummy_show->id)[1];
            $dummy_show->hosts = User::whereIn('id', $track_managers[$track_id] ?? [])->get();
        }
        $start = Carbon::now()->modify('next '.$this->day)
                              ->setTimeFromTimeString($this->start);
        $end = $start->copy()->setTimeFromTimeString($this->end);
        if ($end <= $start) {
            $end->addDay();
        }

        $collection = $this->term->shows->concat($track_shows);
        do {
            $show = $collection->where('day', $end->format('l'))->where('start', $end->format('H:i'))->first();
            $end->addMinutes(30);
        } while ($show == null);

        return $show;
    }
}
