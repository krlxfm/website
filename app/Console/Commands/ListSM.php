<?php

namespace KRLX\Console\Commands;

use KRLX\User;
use KRLX\Track;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ListSM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board:sme';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the list of potential candidates for Station Manager.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $weekly_tracks = Track::where(['active' => true, 'weekly' => true])->get()->pluck('id')->all();
        $candidates = User::where('year', date('Y') + 1)->orderBy('email')->with('shows', 'points')->get();

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

        $candidate_table = $candidates->map(function ($user) {
            return [$user->name, $user->year, $user->email];
        });

        $this->line('Board-Eligible Current Juniors:');
        $this->table(['Name', 'Year', 'Email'], $candidate_table);
        $this->comment('To unlock Station Manager for a candidate, run "php artisan board:sm <user>" with the');
        $this->comment('candidate\'s email address or Carleton NetID, then run "php artisan board:unlock".');
    }
}
