<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
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
        'id', 'on_air', 'off_air'
    ];

    /**
     * The attributes that should be hidden from API calls.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be mutated to non-date things.
     *
     * @var array
     */
    protected $casts = [
        'accepting_applications' => 'boolean'
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
        'off_air'
    ];

    /**
     * Computes the "long form" name of a term.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $components = explode('-', $this->id);
        return str_replace('_', ' ', title_case($components[1])).' '.$components[0];
    }
}
