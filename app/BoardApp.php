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
        'interview_schedule' => 'array'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'interview',
    ];
}
