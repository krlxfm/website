<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Boost extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['show_id', 'type', 'user_id'];

    /**
     * The user who this Boost is assigned to.
     *
     * @return KRLX\User
     */
    public function user()
    {
        return $this->belongsTo('KRLX\User');
    }

    /**
     * The show which this Boost is assigned to.
     *
     * @return KRLX\Show
     */
    public function show()
    {
        return $this->belongsTo('KRLX\Show');
    }
}
