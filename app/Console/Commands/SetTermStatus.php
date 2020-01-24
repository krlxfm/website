<?php

namespace KRLX\Console\Commands;

use Illuminate\Console\Command;
use KRLX\Term;

class SetTermStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'term:status {term : The ID of the term to modify.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application acceptance status of a term.';

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
        $term = Term::find($this->argument('term'));
        if (! $term) {
            $this->error("No term could be found with ID {$this->argument('term')}.");

            return 40;
        }

        $statuses = [
            'pending' => 'Early access applications (board members and those with early access can submit)',
            'active' => 'Applications open',
            'closed' => 'Applications closed, schedule still editable',
            'scheduled' => 'Schedule locked',
        ];
        $new_status = $this->choice("What status would you like to apply to {$term->name}?", $statuses, 'active');

        $term->status = $new_status;
        $term->save();

        $this->info("{$term->name} has had its status set to $new_status.");
    }
}
