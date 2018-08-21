<?php

namespace KRLX\Providers;

use Google_Client;
use Google_Service_Calendar;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Google_Client', function($app) {
            $config = [
                'application_name' => config('app.name'),
                'client_id' => config('services.google_calendar.client_id'),
                'client_secret' => config('services.google_calendar.client_secret'),
                'redirect_uri' => config('services.google_calendar.redirect'),
                'access_type' => 'offline',
            ];
            $client = new Google_Client($config);
            $client->setScopes(implode(' ', [Google_Service_Calendar::CALENDAR]));

            $credentialsPath = storage_path('google.json');
            if (file_exists($credentialsPath)) {
                $accessToken = json_decode(file_get_contents($credentialsPath), true);
                $client->setAccessToken($accessToken);
                if ($client->isAccessTokenExpired()) {
                    $refreshToken = $client->getRefreshToken();
                    $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    $accessToken['refresh_token'] = $refreshToken;
                    file_put_contents($credentialsPath, json_encode($accessToken));
                    $client->setAccessToken($accessToken);
                }
            }

            return $client;
        });

        $this->app->bind('Google_Service_Calendar', function($app) {
            return new Google_Service_Calendar($app->make('Google_Client'));
        });
    }
}
