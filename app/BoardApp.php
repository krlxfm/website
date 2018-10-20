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
}
