<?php

namespace KRLX\Console\Commands;

use Google_Client;
use Illuminate\Console\Command;

class CalendarConnect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krlx:gcc {--a|auth : Perform initial authentication if tokens are not set up}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check connection to Google Calendar';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Google_Client $client)
    {
        if (! env('GOOGLE_CLIENT_ID') or ! env('GOOGLE_CLIENT_SECRET') or ! env('GOOGLE_REDIRECT_URL')) {
            $this->error('Google credentials are missing! Please check your .env and ensure that GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and GOOGLE_REDIRECT_URL have all been populated.');

            return 40;
        }

        if ($client->isAccessTokenExpired() and $this->option('auth')) {
            $authUrl = $client->createAuthUrl();
            $this->comment('Open the following link in your browser. When prompted, sign in using the Google account that owns the calendar which should be published on.');
            $this->line($authUrl);
            $code = $this->ask('Enter the verification code presented');

            $accessToken = $client->fetchAccessTokenWithAuthCode($code);
            if (array_key_exists('error', $accessToken)) {
                $this->error('Could not connect to Google: '.$accessToken['error']);

                return 41;
            }

            file_put_contents(storage_path('google.json'), json_encode($accessToken));
            $this->info('Authentication successful!');
        } elseif ($client->isAccessTokenExpired()) {
            $this->error('Google authentication token is missing. Run this command on the command line with -a or --auth and follow the prompts to set up Google Calendar.');

            return 42;
        } else {
            $this->info('Linus, we\'re set: Google Calendar connection is alive and well.');
        }
    }
}
