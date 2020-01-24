<?php

namespace KRLX\Console\Commands;

use KRLX\User;
use KRLX\Track;
use KRLX\Config;
use KRLX\Permission;
use Illuminate\Console\Command;

class IssueXP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board:unlock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unlock Board applications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Starting to check board app configs...');
        $this->line('You may need to widen your terminal to see everything.');
        $this->line('');
        $this->line('Common questions are as follows:');

        $common_questions = json_decode(Config::valueOr('common questions', '[]'), true);
        $common_data = [];
        foreach ($common_questions as $idx => $question) {
            $common_data[] = [$idx, $question];
        }

        $this->table(['Q#', 'Question Text'], $common_data);

        if (!$this->confirm('Do you wish to continue with these common questions?')) {
            $this->comment('Please update the common questions in the "configs" table in the database and then try again.');
            $this->comment('Common questions must be a JSON-valid array of strings.');
            return 0;
        }
        $this->info('✓ Common questions OK.');
        $this->line('');
        $this->line('Interview time options are as follows (candidates can choose times at 15-minute intervals, excluding the end time):');

        $interview_opts = json_decode(Config::valueOr('interview options', '[]'), true);
        $interview_data = [];
        $correct_year = true;
        foreach ($interview_opts as $option) {
            $interview_data[] = [$option['date'], $option['start'], $option['end']];
            if (intval(substr($option['date'], 0, 4)) !== date('Y')) $correct_year = false;
        }
        $this->table(['Date', 'Start Time', 'End Time'], $interview_data);

        if (!$correct_year) {
            $this->error('Error: The interview dates are not using the correct year.');
            $this->error('Please revise the interview options in the database "configs" table and try again.');
            return 0;
        } else if (!$this->confirm('Do you wish to continue with these interview options?')) {
            $this->comment('Please update the interview options in the "configs" table in the database and then try again.');
            $this->comment('Interview options must be a JSON-valid array of objects, each containing date [yyyy-mm-dd], start, and end times [24-hour hh:mm].');
            return 0;
        }
        $this->info('✓ Interview dates and times OK.');

        if (!$this->confirm('Have you verified the questions in each position and do you want to use them?')) {
            $this->comment('Please update the questions belonging to each position in the "positions" database table and try again.');
            $this->comment('Position-specific questions must be a JSON-valid array of strings.');
            return 0;
        }

        // Get the list of users who should be cleared to apply to the board.
        // Requirements:
        // - Have a class year strictly greater than the current year, AND
        // - Have at least one "issued" point OR a "provisioned" point in the
        //   current term and at least one submitted and scheduled weekly show
        //   in which the user is a host, AND
        // - The most recently issued point cannot have been withheld
        // For Station Manager candidates, the class year must be EXACTLY one
        // greater than the current year, PLUS the user must have had at least
        // one prior term of service on the Board.
        $this->line('Checking users for eligibility...');

        $weekly_tracks = Track::where(['active' => true, 'weekly' => true])->get()->pluck('id')->all();
        $candidates = User::where([['year', '>', date('Y')]])->with('shows', 'points')->get();

        $bar = $this->output->createProgressBar($candidates->count());
        $bar->start();

        $candidates = $candidates->reject(function ($user) use ($bar, $weekly_tracks) {
            $bar->advance();

            if ($user->points->count() === 0) return true;
            $most_recent_pt = $user->points->sortByDesc('created_at')->first();
            if ($most_recent_pt->status === 'withheld') return true;
            if ($most_recent_pt->status === 'provisioned') {
                return $user->shows->where('term_id', $most_recent_pt->term_id)->filter(function ($show) use ($weekly_tracks) {
                    return in_array($show->track_id, $weekly_tracks) and $show->submitted;
                })->count() === 0;
            }
        });
        $bar->finish();
        $this->line('');
        $this->info('✓ '.$candidates->count().' candidates identified.');

        if (!$this->confirm('Do you wish to unlock board applications now? This is your last chance to stop.')) {
            $this->comment('Board applications have remained locked.');
            return 0;
        }
        $this->line('Unlocking applications...');
        $bar = $this->output->createProgressBar($candidates->count());
        $bar->start();
        $candidates->each(function ($user) use ($bar) {
            $user->givePermissionTo('apply for board seats');
            $bar->advance();
        });
        $bar->finish();
        $this->line('');
        $this->info('✓ Board applications have been unlocked.');

        $this->comment('Station Manager applications are handled separately!');
        $this->comment('To authorize Station Manager candidates, run "php artisan board:sm <user>", where <user> is the');
        $this->comment('Station Manager candidate\'s Carleton NetID or Carleton email address. Candidates must have had at');
        $this->comment('least one term of service on the Board (including interim service) and must have a class year of '.(date('Y') + 1));
        $this->comment('in order to be eligible for Station Manager.');
    }
}
