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
        $show_data = $term->showsInPriorityOrder(true);
        $shows = array_combine($show_data->pluck('id')->all(), $show_data->all());

        return view('schedule.build', compact('term', 'shows'));
    }
}
