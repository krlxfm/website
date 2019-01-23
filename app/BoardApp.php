<?php

namespace KRLX;

use KRLX\Events\BoardAppCreating;
use Illuminate\Database\Eloquent\Model;

class BoardApp extends Model
{
    /**
     * The events that should be dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => BoardAppCreating::class,
    ];

    /**
     * The attributes that need type casting.
     *
     * @var array
     */
    protected $casts = [
        'common' => 'array',
        'interview_schedule' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'interview',
    ];

    /**
     * Board Apps are generally permissive (this makes logic easier).
     * These are the attributes we do NOT want to be mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'user_id', 'year', 'created_at', 'updated_at', 'submitted', 'interview',
    ];

    /**
     * Each BoardApp contains one or more PositionApps.
     *
     * @return Eloquent\Collection<KRLX\PositionApp>
     */
    public function positions()
    {
        return $this->hasMany('KRLX\PositionApp')->orderBy('order');
    }

    /**
     * BoardApps have one User assigned to them.
     *
     * @return KRLX\User
     */
    public function user()
    {
        return $this->belongsTo('KRLX\User');
    }

    /**
     * Determines if the common questions have been completed.
     * @return bool
     */
    public function getCommonCompleteAttribute()
    {
        $empty_common = collect($this->common)->filter(function ($item) {
            return empty($item);
        })->count();

        return $empty_common == 0;
    }
}
