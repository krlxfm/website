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
    protected $signature = 'krlx:gcc';

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
    public function handle()
    {
        if (!env('GOOGLE_CLIENT_ID') or !env('GOOGLE_CLIENT_SECRET') or !env('GOOGLE_REDIRECT_URL')) {
            $this->error('Google credentials are missing! Please check your .env and ensure that GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and GOOGLE_REDIRECT_URL have all been populated.');
            return 1;
        }
        $this->line('Contacting Google...');
    }
}
