<?php

namespace KRLX\Providers;

use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            $body = unserialize($event->job->payload()['data']['command']);
            if ($event->job->resolveName() == 'KRLX\Jobs\PublishShow' or $event->job->resolveName() == 'KRLX\Jobs\FinalPublishShow') {
                $data = array_wrap(json_decode(file_get_contents(storage_path('app/publish')), true));
                $data['show'] = $body->show->id;
                if (array_key_exists('position', $data)) {
                    $data['position'] += 1;
                } else {
                    $data['position'] = 1;
                }
                file_put_contents(storage_path('app/publish'), json_encode($data));
            }
        });

        Queue::after(function (JobProcessed $event) {
            if ($event->job->resolveName() == 'KRLX\Jobs\PublishShow' or $event->job->resolveName() == 'KRLX\Jobs\FinalPublishShow') {
                $data = array_wrap(json_decode(file_get_contents(storage_path('app/publish')), true));
                if (isset($data['position']) and isset($data['max']) and $data['position'] == $data['max']) {
                    file_put_contents(storage_path('app/publish'), json_encode([]));
                }
            }
        });

        Schema::defaultStringLength(190);

        Menu::macro('main', function () {
            $shows_dropdown = Menu::new()
                ->withoutParentTag()
                ->setWrapperTag('div')
                ->addClass('dropdown-menu dropdown-menu-right')
                ->addParentClass('dropdown')
                ->addItemClass('dropdown-item')
                ->prepend('<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Radio shows</a>')
                ->route('shows.my.other', 'My shows')
                ->route('shows.create', 'New show')
                ->route('shows.join', 'Join a show')
                ->htmlIf(Auth::user()->eligibleBoosts()->count() > 0, '<div class="dropdown-divider"></div>')
                ->routeIf(Auth::user()->eligibleBoosts()->count() > 0, 'boost.index', 'Priority upgrades')
                ->htmlIf(Auth::user()->hasAnyPermission(['see all applications', 'see all DJs']), '<div class="dropdown-divider"></div>')
                ->routeIfCan('see all applications', 'shows.all', 'All shows')
                ->routeIfCan('see all DJs', 'shows.djs', 'DJ roster')
                ->htmlIfCan('build schedule', '<div class="dropdown-divider"></div>')
                ->routeIfCan('build schedule', 'schedule.build', 'Schedule builder')
                ->setActiveClassOnLink();

            $board_dropdown = Menu::new()
                ->withoutParentTag()
                ->setWrapperTag('div')
                ->addClass('dropdown-menu dropdown-menu-right')
                ->addParentClass('dropdown')
                ->addItemClass('dropdown-item')
                ->prepend('<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Board</a>')
                ->route('board.meet', 'Meet')
                ->setActiveClassOnLink();

            $menu = Menu::new()
                ->addClass('navbar-nav ml-auto')
                ->addItemParentClass('nav-item')
                ->setActiveFromRequest()
                ->route('home', 'Home')
                ->submenu($shows_dropdown)
                ->submenu($board_dropdown)
                ->route('profile', 'Profile')
                ->route('logout', 'Sign out')
                ->each(function (Link $link) {
                    $link->addClass('nav-link');
                });

            if (app('request')->route()->getName() == 'shows.my') {
                $menu->setActive(route('shows.my.other'));
            }

            return $menu;
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
