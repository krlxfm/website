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

    /**
     * The term which this Boost is assigned to.
     *
     * @return KRLX\Show
     */
    public function term()
    {
        return $this->belongsTo('KRLX\Term');
    }

    /**
     * Guard on the show_id attribute. Show IDs can be set freely if neither
     * show nor term is set. Once either of these are present, new Show IDs
     * must be within the same term.
     *
     * @param  string  $value
     * @return void
     */
    public function setShowIdAttribute($value)
    {
        $show = Show::find($value);
        if (! $show) {
            return;
        }

        $multi_boost_check = ($this->type == 'zone' or $show->boosts()->where('type', $this->type)->count() == 0);
        $eligible_check = $show->track->boostable;
        $same_term_check = true;
        if ($this->term_id) {
            $same_term_check = ($this->term_id == $show->term_id);
        } elseif ($this->show_id) {
            $same_term_check = ($this->show->term_id == $show->term_id);
        }

        if ($multi_boost_check and $eligible_check and $same_term_check) {
            $this->attributes['show_id'] = $value;
        }
    }
}
