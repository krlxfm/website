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
        User::chunk(50, function ($users) {
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            foreach ($users as $user) {
                $user->revokePermissionTo('apply for board seats');
                $bar->advance();
            }
            $bar->finish();
        });
        $this->info('✓ Board applications have been locked.');
    }
}
