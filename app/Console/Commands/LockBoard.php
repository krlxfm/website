<?php

namespace KRLX\Console\Commands;

use Illuminate\Console\Command;
use KRLX\Position;
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
        if ($this->confirm('Are you sure you wish to revoke all users\' permission to apply for board seats?')) {
            User::chunk(50, function ($users) {
                foreach ($users as $user) {
                    $user->revokePermissionTo('apply for board seats');
                }
            })
        }
    }
}
