<?php

namespace KRLX\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use KRLX\Mail\ScheduleLocked;
use KRLX\Show;

class FinalPublishShow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $show;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Show $show)
    {
        $this->show = $show;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->show->start != $this->show->published_start or $this->show->start != $this->show->published_start or $this->show->start != $this->show->published_start) {
            PublishShow::dispatch($this->show, false);
        }

        if ($this->show->day and $this->show->start and $this->show->end) {
            Mail::to($this->show->hosts)->queue(new ScheduleLocked($this->show));
        }
    }
}
