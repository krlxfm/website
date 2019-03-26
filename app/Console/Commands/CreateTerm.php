<?php

namespace KRLX\Console\Commands;

use KRLX\Term;
use KRLX\User;
use KRLX\Track;
use KRLX\Config;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateTerm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'term:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and activate a term for radio applications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Welcome to the KRLX Mission Control Term Creator.');
        $this->info('You will need to enter some information about the upcoming term, which can be retrieved from the Carleton academic calendar: https://apps.carleton.edu/calendar/academic/');

        $start = null;
        do {
            try {
                $start = Carbon::parse($this->ask('What is the date that classes begin?'));
                if (! $this->confirm("Start date parsed as {$start->format('l, F j, Y')}. Is this correct?")) {
                    $start = null;
                }
            } catch (\Exception $e) {
                $this->error('Invalid date provided. Please try again.');
            }
        } while ($start === null);

        $last_day = [
            'Mon' => Carbon::WEDNESDAY,
            'Wed' => Carbon::FRIDAY,
            'Fri' => Carbon::MONDAY,
        ];

        $end = $start->copy()->addWeeks(9)->next($last_day[$start->format('D')]);

        if (! $this->confirm("End date calculated as {$end->format('l, F j, Y')}. Is this correct?")) {
            do {
                try {
                    $end = Carbon::parse($this->ask('What is the date that classes end?'));
                    if (! $this->confirm("End date parsed as {$start->format('l, F j, Y')}. Is this correct?")) {
                        $end = null;
                    }
                } catch (\Exception $e) {
                    $this->error('Invalid date provided. Please try again.');
                }
            } while ($end === null);
        }
        $end->setTimeFromTimeString('21:00:00');

        $termNames = ['WI', 'WI', 'WI', 'SP', 'SP', 'SP', 'FA', 'FA', 'FA', 'FA', 'FA', 'WI', 'WI'];
        $termId = $start->format('Y').'-'.$termNames[$start->format('n')];

        if (! $this->confirm("Is $termId the correct term ID?")) {
            $termId = $this->ask('Please enter the correct term ID.');
        }

        if (Term::find($termId) !== null) {
            $this->error("A term already exists with ID $termId. Please edit its settings with Tinker or a direct database connection.");

            return 40;
        }

        $onAirDate = $end->copy()->subWeeks(8);
        if (substr($termId, -3) == '-FA') {
            $onAirDate->addWeek();
        }
        $appCloseDate = $onAirDate->copy()->subDays(4)->setTimeFromTimeString('12:00:00');

        $user = null;
        do {
            $email = $this->ask('Please enter the email address of the Programming Director');
            $user = User::whereEmail($email)->first();
            if ($user === null) {
                $this->error("No users could be found with the email $email. Please try again.");
            }
        } while ($user === null);

        $tracks = Track::where([['active', true], ['weekly', false]])->get();
        $track_managers = $tracks->reduce(function ($carry, $track) use ($user) {
            $track_managers[$track->id] = [$user->id];

            return $track_managers;
        }, []);

        $term = new Term;
        $term->id = $termId;
        $term->on_air = $onAirDate;
        $term->off_air = $end;
        $term->applications_close = $appCloseDate;
        $term->track_managers = $track_managers;
        $term->boosted = (substr($termId, -3) !== '-FA');

        $statuses = [
            'No - leave applications closed for everyone for now',
            'Limited access - allow board members and those with early access to apply, otherwise keep applications closed',
            'Yes - open applications now',
        ];
        $status_keys = [
            'No - leave applications closed for everyone for now' => 'new',
            'Limited access - allow board members and those with early access to apply, otherwise keep applications closed' => 'pending',
            'Yes - open applications now' => 'active',
        ];
        $term->status = $status_keys[$this->choice('Would you like to open applications now?', $statuses, 1)];
        $term->save();

        Config::set('active term', $term->id);

        $headers = ['Field', 'Value'];
        $data = [
            ['Term ID', $term->id],
            ['Term Name', $term->name],
            ['Status', $term->status],
            ['Classes begin', $start->format('l, F j, Y')],
            ['Applications close', $appCloseDate->format('l, F j, Y g:i A')],
            ['Schedule locks', $onAirDate->copy()->subDay()->format('l, F j, Y g:i A')],
            ['On air', $onAirDate->format('l, F j, Y g:i A')],
            ['Off air', $end->format('l, F j, Y g:i A')],
        ];
        foreach ($tracks as $track) {
            $data[] = ["Track manager, {$track->name}", $user->full_name];
        }

        $this->info('The following term has been created:');
        $this->table($headers, $data);
        $this->info('Details can be adjusted in the database or via Artisan later if needed.');

        if ($term->status != 'active') {
            $this->line('');
            $this->comment('Applications are not open to the general community.');
            $this->comment("When you're ready to open applications, run php artisan term:status {$term->id}.");
        }
    }
}
