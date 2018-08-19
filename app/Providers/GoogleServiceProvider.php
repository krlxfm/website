<?php

namespace KRLX\Providers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $client = new Google_Client();
        $client->setApplicationName(config('app.name'));
        $client->setScopes(implode(' ', [Google_Service_Calendar::CALENDAR]));
        $client->setClientId(config('services.google_calendar.client_id'));
        $client->setClientSecret(config('services.google_calendar.client_secret'));
        $client->setRedirectUri(config('services.google_calendar.redirect'));
        $client->setAccessType('offline');

        $credentialsPath = storage_path('google.json');
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
            $client->setAccessToken($accessToken);
            if ($client->isAccessTokenExpired()) {
                $refreshToken = $client->getRefreshToken();
                $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $accessToken["refresh_token"] = $refreshToken;
                file_put_contents($credentialsPath, json_encode($accessToken));
            }
        }

        $this->app->instance('Google_Client', $client);
    }
}
