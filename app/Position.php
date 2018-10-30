<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $casts = [
        'app_questions' => 'array'
    ];
}
