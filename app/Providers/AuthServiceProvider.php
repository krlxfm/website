<?php

namespace KRLX\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'KRLX\BoardApp' => 'KRLX\Policies\BoardAppPolicy',
        'KRLX\Boost' => 'KRLX\Policies\BoostPolicy',
        'KRLX\PositionApp' => 'KRLX\Policies\PositionAppPolicy',
        'KRLX\Show' => 'KRLX\Policies\ShowPolicy',
        'KRLX\Track' => 'KRLX\Policies\TrackPolicy',
        'KRLX\Term' => 'KRLX\Policies\TermPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
