<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class PositionApp extends Model
{
    public function position()
    {
        return $this->belongsTo('KRLX\Position');
    }

    public function app()
    {
        return $this->belongsTo('KRLX\BoardApp');
    }
}
