<?php

namespace KRLX;

use KRLX\Events\PositionAppCreating;
use Illuminate\Database\Eloquent\Model;

class PositionApp extends Model
{
    protected $fillable = [
        'position_id', 'order', 'responses',
    ];

    protected $dispatchesEvents = [
        'creating' => PositionAppCreating::class,
    ];

    protected $casts = [
        'responses' => 'array',
    ];

    public function position()
    {
        return $this->belongsTo('KRLX\Position');
    }

    public function board_app()
    {
        return $this->belongsTo('KRLX\BoardApp');
    }

    public function getCompleteAttribute()
    {
        return $this->complete();
    }

    /**
     * For quick validation, returns whether or not all questions have been
     * successfully answered.
     *
     * @return bool
     */
    public function complete()
    {
        foreach ($this->position->app_questions as $question) {
            if (! array_key_exists($question, $this->responses) or empty($this->responses[$question])) {
                return false;
            }
        }

        return true;
    }
}
