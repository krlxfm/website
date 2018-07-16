<?php

namespace KRLX\Providers;

use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(190);

        Menu::macro('main', function () {
            return Menu::new()
                ->addClass('navbar-nav ml-auto')
                ->addItemParentClass('nav-item')
                ->addItemClass('nav-link')
                ->route('home', 'Home')
                ->route('shows.my', 'My shows')
                ->route('shows.create', 'New show')
                ->route('logout', 'Sign out')
                ->setActiveFromRequest();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
