<?php

namespace KRLX\Console\Commands;

use KRLX\Show;
use KRLX\Point;
use Illuminate\Console\Command;

class IssueXP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krlx:xp {--dry-run : Simulates the results without affecting the database.}';

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
        $dry = $this->option('dry-run');

        $points = Point::where('status', 'provisioned')->get();

        if ($points->count() == 0) {
            $this->info('There are no pending experience points.');

            return 0;
        } elseif ($dry) {
            $this->info('Running in dry-run mode. The database will not be affected.');
        } elseif (! $this->confirm('There are experience points pending, would you like to continue?')) {
            return 0;
        }

        $shows = Show::where('priority', null)->count();
        $bar = $this->output->createProgressBar($shows->count());
        $bar->start();
        foreach ($shows as $show) {
            if (! $dry) {
                $show->priority = $show->priority_code;
                $show->save();
            }
            $bar->advance();
        }
        $bar->finish();

        $bar = $this->output->createProgressBar($points->count());
        $bar->start();
        $no_shows = [];
        $no_eligible_shows = [];
        $issued = [];
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

            if ($eligible) {
                $issued[] = $point;
            }
            if (! $dry) {
                // $point->status = $eligible ? 'issued' : 'ineligible';
                // $point->save();
            }

            $bar->advance();
        }
        $bar->finish();
        $this->line('');

        if ($dry) {
            $this->info(count($issued).' '.str_plural('point', $issued).' will be issued.');
            foreach ($issued as $point) {
                $this->line("[{$point->id}, {$point->term_id}] {$point->user->full_name}");
            }
            $this->info('Run this command without the --dry-run option to issue points.');
        } else {
            $this->info(count($issued).' '.str_plural('point', $issued).' issued.');
        }
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
