<?php

namespace KRLX;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use KRLX\Events\UserCreating;
use KRLX\Notifications\ResetPassword;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'photo', 'year',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be added to arrays.
     *
     * @var array
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * The events that should be dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => UserCreating::class,
    ];

    /**
     * Returns the shows that the user is a member of.
     *
     * @return Eloquent\Collection<KRLX\Show>
     */
    public function shows()
    {
        return $this->belongsToMany('KRLX\Show')
                    ->withPivot('accepted')
                    ->wherePivot('accepted', true)
                    ->withTimestamps()
                    ->as('membership');
    }

    /**
     * Returns all Priority Boost access tickets that belong to the user.
     *
     * @return Eloquent\Collection<KRLX\Boost>
     */
    public function boosts()
    {
        return $this->hasMany('KRLX\Boost');
    }

    /**
     * Returns all Board applications that the user has created.
     *
     * @return Eloquent\Collection<KRLX\BoardApp>
     */
    public function board_apps()
    {
        return $this->hasMany('KRLX\BoardApp');
    }

    /**
     * Returns the experience points that belong to the user.
     *
     * @return Eloquent\Collection<KRLX\Point>
     */
    public function points()
    {
        return $this->hasMany('KRLX\Point');
    }

    /**
     * Returns the shows that the user has been invited to, but not joined yet.
     *
     * @return Eloquent\Collection<KRLX\Show>
     */
    public function invitations()
    {
        return $this->belongsToMany('KRLX\Show')
                    ->withPivot('accepted')
                    ->wherePivot('accepted', false)
                    ->withTimestamps()
                    ->as('membership');
    }

    /**
     * Generate the Priority object corresponding to this user.
     *
     * @return KRLX\Priority
     */
    public function getPriorityAttribute()
    {
        $priority = new Priority;
        $priority->terms = $this->points()->where('status', 'issued')->count();
        $priority->year = $this->year;

        return $priority;
    }

    /**
     * Computes what the user's priority was before the specified term began.
     *
     * @param  string  $term
     * @return KRLX\Priority
     */
    public function priorityAsOf(string $termID)
    {
        $term = Term::find($termID);
        if (! $term) {
            return new Priority(0);
        }
        $priority = $this->priority;

        $priority->terms = $this->points()
                                ->whereHas('term', function ($query) use ($term) {
                                    $query->where('on_air', '<', $term->on_air);
                                })
                                ->where('status', 'issued')
                                ->count();

        return $priority;
    }

    /**
     * Function to determine if the user has any boosts available in the current
     * academic term.
     *
     * @return bool
     */
    public function eligibleBoosts()
    {
        $term = Term::orderByDesc('on_air')->first();
        $boosts = $this->boosts()->with('show')->get();

        return $boosts->filter(function ($boost) use ($term) {
            if ($boost->term_id) {
                return $boost->term_id == $term->id;
            } else {
                return ! $boost->show or $boost->show->term_id == $term->id;
            }
        });
    }

    /**
     * Gets the user's "full name". For Carls and alumni, this appends the class
     * name onto the end of the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        if ($this->year >= 1000) {
            return $this->name." '".substr($this->year, -2);
        } else {
            return $this->name;
        }
    }

    /**
     * Gets the user's "public name", seen by folks who aren't logged in.
     * TODO: Implement last-name splits.
     *
     * @return string
     */
    public function getPublicNameAttribute()
    {
        return $this->nickname ?? $this->name;
    }

    /**
     * Send a password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
