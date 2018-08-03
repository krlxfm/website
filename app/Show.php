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
     * Compute how much of a priority boost the show should get. Returns "Z+N"
     * if the show is entitled to an N-zone boost, "S" if the show is titled to
     * skipping track order, or "A" if the priority should be set to A1 within
     * the track.
     *
     * @return string|null
     */
    public function getBoostAttribute()
    {
        if (! $this->boosted) {
            return;
        }

        $string = '';
        foreach ($this->hosts as $host) {
            if ($host->membership->boost == 'S') {
                $string = 'S';
                break;
            } elseif ($host->membership->boost == 'A1') {
                $string = 'A1';
            } elseif ($host->membership->boost[0] == '+' and $string != 'A1') {
                $zones = intval(substr($host->membership->boost));
                $current_zones = ($string[0] == 'Z' ? intval(substr($string, 2)) : 0);
                if ($zones > $current_zones) {
                    $string = 'Z+'.$zones;
                }
            }
        }

        return $string;
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
        if ($this->boosted and $this->boost[0] == 'Z') {
            $terms += intval(substr($this->boost, 2));
        } elseif ($this->boosted and $this->boost == 'A1') {
            return 'A1';
        }

        if ($this->track->zone) {
            $zone = $this->track->zone;
        } elseif ($terms >= count(config('defaults.priority.terms'))) {
            $zone = config('defaults.priority.default');
        } else {
            $zone = config('defaults.priority.terms')[$terms];
        }

        $year = $priorities->min->year;
        if ($this->track->group !== null) {
            $group = $this->track->group;
        } elseif ($year == 0) {
            $zone = config('defaults.priority.none');
        } elseif ($year >= count(config('defaults.status_codes')) and $year < 1000) {
            $zone = config('defaults.priority.default');
        } elseif (strlen($zone) == 2) {
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
