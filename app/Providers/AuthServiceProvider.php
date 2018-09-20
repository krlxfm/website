<?php

namespace KRLX\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'KRLX\Boost' => 'KRLX\Policies\BoostPolicy',
        'KRLX\Show' => 'KRLX\Policies\ShowPolicy',
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
