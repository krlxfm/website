<?php

namespace KRLX\Console\Commands;

use Illuminate\Console\Command;
use KRLX\User;

class LockBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board:lock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lock Board applications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirm('Are you sure you wish to revoke all users\' permission to apply for board seats?')) {
            $this->error('Aborting changes.');

            return 0;
        }
        $this->info('Identifying eligible applicants...');
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        $users = User::all();
        $eligible = $users->filter(function ($user) {
            $bar->advance();

            return $user->hasPermissionTo('apply for board seats');
        });
        $bar->finish();

        $this->info('Revoking permissions...');
        $bar = $this->output->createProgressBar($eligible->count());
        $bar->start();
        foreach ($eligible as $user) {
            $user->revokePermissionTo('apply for board seats');
            $bar->advance();
        }
        $bar->finish();

        $this->info('✓ Board applications have been locked.');
    }
}
