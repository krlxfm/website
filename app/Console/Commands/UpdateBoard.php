<?php

namespace KRLX\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use KRLX\User;

class UpdateBoard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the roles of \'board\' of graduating and incoming board members.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Welcome to the KRLX Board Application Unlock Script.');
        $this->line('You may need to widen your terminal to see everything.');
        if (! $this->confirm('Have you verified that all titles are properly updated in the database?')) {
            $this->comment('Please update the database such that:');
            $this->comment(' 1. Graduating board members have \'Emeritus ...\' in their user \'title\' column');
            $this->comment(' 2. New and returning board members for the coming term have titles updated to their new positions');
            $this->comment(' 3. All other non-board members for the coming year have \'KRLX Community\' as their title');

            return 0;
        }
        $this->line('Getting current, graduating, returning, and new board members...');
        $users = User::all();
        $bar = $this->output->createProgressBar($users->count());
        $curr_board = collect([]);
        $grad_board = collect([]);
        $ret_board = collect([]);
        $new_board = collect([]);
        $users->each(function ($user) use ($bar, $curr_board, $grad_board, $ret_board, $new_board) {
            $bar->advance();
            if ($user->hasRole('board')) {
                $curr = true;
                $curr_board->push($user);
            } else {
                $curr = false;
            }
            $title = $user->title;
            if ($title !== 'KRLX Community') {
                if ($curr) {
                    if (Str::startsWith($title, 'Emeritus')) {
                        $grad_board->push($user);
                    } else {
                        $ret_board->push($user);
                    }
                } elseif (! Str::startsWith($title, 'Emeritus')) {
                    $new_board->push($user);
                }
            }
        });
        $bar->finish();
        $this->line('');
        $this->info('✓ Successfully identified users.');
        $this->line('');

        $tallies = [];
        $tallies[] = [$curr_board->count(), $grad_board->count(), $ret_board->count(), $new_board->count()];
        $this->table(['Current', 'Graduating', 'Returning', 'New'], $tallies);

        $grad_names = [];
        foreach ($grad_board as $user) {
            $grad_names[] = [$user->name];
        }
        $this->table(['Graduating Members'], $grad_names);

        $ret_names = [];
        foreach ($ret_board as $user) {
            $ret_names[] = [$user->name];
        }
        $this->table(['Returning Members'], $ret_names);

        $new_names = [];
        foreach ($new_board as $user) {
            $new_names[] = [$user->name];
        }
        $this->table(['New Members'], $new_names);

        $accounted_for = $grad_board->concat($ret_board);
        $unaccounted_for = $curr_board->diff($accounted_for);
        if ($unaccounted_for->count() != 0) {
            $this->line('The following users have board permissions but are not titled \'Emeritus\' or another board position:');
            foreach ($unaccounted_for as $user) {
                $this->line($user->name);
            }
            if (! $this->confirm('Were these users either interim members or not re-elected? If you\'d like to continue anyway, type yes.')) {
                $this->comment('Best figure out why they have those roles.');
                $this->comment('All changes aborted.');

                return 0;
            }
        }

        if (! $this->confirm('Do you wish to apply updated roles now? This is your last chance to stop.')) {
            $this->comment('Changes have been aborted.');

            return 0;
        }
        $this->line('Setting graduating board members to \'emeritus\' role...');
        $bar = $this->output->createProgressBar($grad_board->count());
        $grad_board->each(function ($user) use ($bar) {
            $user->removeRole('board');
            $user->assignRole('emeritus');
            $bar->advance();
        });
        $bar->finish();
        $this->line('');
        $this->info('✓ Graduating members complete.');

        $this->line('Setting new board members to \'board\' role...');
        $bar = $this->output->createProgressBar($new_board->count());
        $new_board->each(function ($user) use ($bar) {
            $user->assignRole('board');
            $bar->advance();
        });
        $bar->finish();
        $this->line('');
        $this->info('✓ New members complete.');

        $unaccounted_for->each(function ($user) {
            if ($this->confirm('Would you like to remove \'board\' role for '.$user->name.'?')) {
                $user->removeRole('board');
            }
        });

        $this->line('');
        $this->info('✓ All roles successfully updated.');
    }
}
