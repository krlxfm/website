<?php

namespace KRLX;

use KRLX\Events\UserCreating;
use Laravel\Passport\HasApiTokens;
use KRLX\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'photo', 'year'
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
                    ->withPivot('accepted', 'boost')
                    ->wherePivot('accepted', true)
                    ->withTimestamps()
                    ->as('membership');
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
                    ->withPivot('accepted', 'boost')
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
        $priority->terms = $this->points()->where('status', 'issued')->get()->count();
        $priority->year = $this->year;

        return $priority;
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
