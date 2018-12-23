<?php

namespace KRLX\Console\Commands;

use KRLX\Point;
use Illuminate\Console\Command;

class IssueXP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krlx:xp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Issue experience points to eligible DJs.';

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
        $points = Point::where('status', 'provisioned')->get();

        if ($points->count() == 0) {
            $this->info('There are no pending experience points.');

            return 0;
        }

        $bar = $this->output->createProgressBar($points->count());
        $bar->start();
        $no_shows = [];
        $no_eligible_shows = [];
        foreach ($points as $point) {
            // Points here are not withheld, so now we need to check if eligible shows were completed.
            $eligible = true;

            if ($point->user->shows->where('term_id', $point->term_id)->count() == 0) {
                $no_shows[] = $point;
                $eligible = false;
            } elseif ($point->user->shows->where('term_id', $point->term_id)->where('track.awards_xp', true)->count() == 0) {
                $no_eligible_shows[] = $point;
                $eligible = false;
            }

            $point->status = $eligible ? 'issued' : 'ineligible';
            $point->save();

            $bar->advance();
        }
        $bar->finish();
        $this->line('');

        $successful_points = $points->count() - count($no_shows) - count($no_eligible_shows);
        $this->info("Issued $successful_points ".str_plural('point', $successful_points));
        if (count($no_shows) > 0) {
            $this->comment('The following '.str_plural('host', count($no_shows)).' could not be issued points because they did not have any shows on file at all:');
            foreach ($no_shows as $point) {
                $this->line("[{$point->id}, {$point->term_id}] {$point->user->full_name}");
            }
        }
        if (count($no_eligible_shows) > 0) {
            $this->comment('The following '.str_plural('host', count($no_eligible_shows)).' could not be issued points because, while they did participate in at least one show, that show is not eligible for XP:');
            foreach ($no_eligible_shows as $point) {
                $this->line("[{$point->id}, {$point->term_id}] {$point->user->full_name}");
            }
        }
    }
}
