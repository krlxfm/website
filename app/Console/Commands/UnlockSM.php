<?php

namespace KRLX\Console\Commands;

use KRLX\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class UnlockSM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board:sm {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock the Station Manager application for an eligible candidate.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('user');
        if (!Str::endsWith($email, '@carleton.edu')) {
            $email = $email.'@carleton.edu';
        }
        $user = User::whereEmail($email)->first();

        if (!$user) {
            $this->error("No user with the email $email could be found.");
            return 40;
        }

        $user->givePermissionTo('apply for Station Manager');

        $this->info("✓ {$user->name} ({$user->email}) has been granted access to the Station Manager application.");
        $this->comment('If you have not run "php artisan board:unlock" and all Station Manager candidates have access, do so now.');
        $this->comment('If additional Station Manager candidates need access to the application, run this command again.');
    }
}
