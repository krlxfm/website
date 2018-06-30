<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    /**
     * The attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'on_air', 'off_air'
    ];
}
