<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class PositionApp extends Model
{
    protected $guarded = [
        'position_id', 'order', 'responses'
    ];

    protected $casts = [
        'responses' => 'array'
    ];

    public function position()
    {
        return $this->belongsTo('KRLX\Position');
    }

    public function app()
    {
        return $this->belongsTo('KRLX\BoardApp');
    }
}
