<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;
use Carbon\Carbon;

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

        $times = collect(config('classes.times'));
        $early_classes = $times->filter(function ($value, $key) {
            return collect($value['times'])->pluck('start')->map(function ($value) {
                return Carbon::parse($value);
            })->contains(function ($value, $key) {
                return $value->hour < 9;
            });
        })->keys();

        return view('schedule.build', compact('term', 'shows', 'early_classes'));
    }
}
