<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Show the view for building a schedule.
     *
     * @return void
     */
    public function build(Term $term = null)
    {
        if($term == null) {
            $term = Term::orderByDesc('on_air')->first();
        }
        $shows = array_values($term->showsInPriorityOrder(true)->all());

        return view('schedule.build', compact('term', 'shows'));
    }
}
