<?php

namespace KRLX;

use KRLX\Events\ShowCreating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
                    ->withPivot('accepted', 'boost')
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
                    ->withPivot('accepted', 'boost')
                    ->as('membership')
                    ->withTimestamps();
    }

    /**
     * Determine whether or not the show has a Priority Boost request on it.
     * Participants MUST BE HOSTS in order to qualify!
     *
     * @return bool
     */
    public function getBoostedAttribute()
    {
        foreach ($this->hosts as $host) {
            if ($host->membership->boost != null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the show's priority string.
     *
     * @return string
     */
    public function getPriorityAttribute()
    {
        $priorities = $this->hosts->pluck('priority');
        $zone = '';
        $group = '';

        $terms = $priorities->max->terms ?? 0;
        if ($this->track->zone) {
            $zone = $this->track->zone;
        } else if ($terms >= count(config('defaults.priority.terms'))) {
            $zone = config('defaults.priority.default');
        } else {
            $zone = config('defaults.priority.terms')[$terms];
        }

        $year = $priorities->min->year;
        if ($this->track->group) {
            $group = $this->track->group;
        } else if ($year >= count(config('defaults.status_codes')) and $year < 1000) {
            $zone = config('defaults.priority.default');
        } else if (strlen($zone) == 2) {
            $group = '';
        } else {
            $group = $year - $this->term->year + ($this->term->boosted ? 1 : 0);
            if ($group <= 0) {
                $group = '';
                $zone = config('defaults.priority.default');
            }
        }

        return $zone.$group;
    }
}
