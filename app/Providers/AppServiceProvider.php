<?php

namespace KRLX\Providers;

use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;
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
            if ($event->job->resolveName() == 'KRLX\Jobs\PublishShow') {
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
            if ($event->job->resolveName() == 'KRLX\Jobs\PublishShow') {
                $data = array_wrap(json_decode(file_get_contents(storage_path('app/publish')), true));
                if (isset($data['position']) and isset($data['max']) and $data['position'] == $data['max']) {
                    file_put_contents(storage_path('app/publish'), json_encode([]));
                }
            }
        });

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
