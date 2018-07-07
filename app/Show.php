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
        'special_times' => 'array'
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
}
