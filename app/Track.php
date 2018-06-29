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
        'name', 'description'
    ];

    /**
     * The events that should be automatically dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => TrackCreating::class
    ];

    /**
     * The attributes that should be type-cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'scheduling' => 'array',
        'etc' => 'array'
    ];
}
