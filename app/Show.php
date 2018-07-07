<?php

namespace KRLX;

use KRLX\Events\ShowCreating;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    /**
     * Attribute overrides to allow for non-integer primary key.
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Attribute overrides to allow for non-integer primary key.
     *
     * @var boolean
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'term_id', 'track_id', 'source'
    ];

    /**
     * The events that should be dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => ShowCreating::class
    ];
}
