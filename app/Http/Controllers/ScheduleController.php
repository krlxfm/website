<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;

class ScheduleController extends Controller
{
    /**
     * Show the view for building a schedule.
     *
     * @return void
     */
    public function build(Term $term = null)
    {
        if ($term == null) {
            $term = Term::orderByDesc('on_air')->first();
        }

        return view('schedule.build', compact('term'));
    }
}
