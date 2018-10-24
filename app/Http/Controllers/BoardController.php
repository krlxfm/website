<?php

namespace KRLX\Http\Controllers;

use KRLX\User;
use KRLX\BoardApp;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display the "Meet the Board" view.
     *
     * @return Illuminate\Http\Response
     */
    public function meet()
    {
        $board = User::role('board')->orderBy('order')->orderBy('email')->get();

        return view('board.meet', compact('board'));
    }

    /**
     * Display the dashboard for a Board application.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('board.start');
    }

    /**
     * Create a board application for the user if one doesn't already exist.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminuate\Http\Response
     */
    public function start(Request $request)
    {
        $this->authorize('create', BoardApp::class);

        if ($request->user()->board_apps()->where('year', date('Y'))->count() == 0) {
            $request->user()->board_apps()->create();
        }

        return redirect()->route('board.app', date('Y'));
    }

    /**
     * Return the user's board application for a given year. For safety, we kick
     * out if the passed-in URL is a string.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function myApplication($year, Request $request)
    {
        $this->authorize('create', BoardApp::class);
        $user = $request->user();

        if (! is_numeric($year)) {
            return redirect()->route('board.index');
        }

        $int_year = (int) $year;
        $app = $user->board_apps()->where('year', $int_year)->first();
        if (! $app) {
            return redirect()->route('board.index');
        }

        $important_fields = ['bio', 'pronouns', 'hometown', 'major'];
        $missing_fields = collect($important_fields)->filter(function($field) use ($user) {
            return $user->{$field} == null;
        });

        $logistics_needed = collect($app->interview_schedule)->values()->unique()->count() == 1;
        $common_needed = collect($app->common)->filter(function($item) { return empty($item); })->count();

        return view('board.app', compact('app', 'missing_fields', 'logistics_needed', 'common_needed'));
    }
}
