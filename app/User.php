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
        'name', 'email', 'password', 'first_name', 'photo',
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
     * The events that should be dispatched.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => UserCreating::class,
    ];

    /**
     * The attributes that should be type-cast.
     *
     * @var array
     */
    protected $casts = [
        'xp' => 'array',
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
        $priority->terms = collect($this->xp)->unique()->count();
        $priority->year = $this->year;
        return $priority;
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
