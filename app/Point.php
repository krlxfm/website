<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    /**
     * The attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term_id', 'user_id',
    ];

    /**
     * The user who holds this point.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('KRLX\User');
    }

    /**
     * The term this point is for.
     *
     * @return Term
     */
    public function term()
    {
        return $this->belongsTo('KRLX\Term');
    }
}
