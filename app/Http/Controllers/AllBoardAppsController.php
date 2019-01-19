<?php

namespace KRLX\Http\Controllers;

use KRLX\Config;
use Carbon\Carbon;
use KRLX\BoardApp;
use Illuminate\Http\Request;

class AllBoardAppsController extends Controller
{
    public function index(Request $request)
    {
        $completed_apps = BoardApp::where([['year', date('Y')], ['submitted', true]])->with('user')->get()->sortBy('user.email');
        $incomplete_apps = BoardApp::where([['year', date('Y')], ['submitted', false]])->with('user')->get()->sortBy('user.email');
        $my_app = $request->user()->board_apps()->where('year', date('Y'))->first();

        return view('board.all.index', compact('completed_apps', 'incomplete_apps', 'my_app'));
    }

    public function pdf(BoardApp $app, Request $request)
    {
        $my_app = $request->user()->board_apps()->where('year', $app->year)->first();
        $redacted_sections = collect();
        if ($my_app and $my_app->id !== $app->id) {
            $my_positions = collect($my_app->positions->pluck('position'))->pluck('id');
            $redacted_sections = $app->positions->filter(function ($pos) use ($my_positions) {
                return $my_positions->contains($pos->position_id);
            })->pluck('position')->pluck('id');
        }

        return view('board.all.pdf', compact('app', 'redacted_sections'));
    }

    public function interviews()
    {
        $apps = BoardApp::where([['year', date('Y')], ['submitted', true]])->with('user')->get()->sortBy('user.email');

        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $dates = [];
        foreach ($interview_options as $option) {
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                $dates[] = $time->copy();
                $time->addMinutes(15);
            }
        }

        return view('board.all.interviews', compact('apps', 'dates'));
    }
}
