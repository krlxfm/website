<?php

namespace KRLX\Http\Controllers;

use KRLX\User;
use KRLX\Config;
use Carbon\Carbon;
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
     * Validate that a request is okay (meaning: valid year).
     *
     * @param  int  $year
     * @param  Illuminate\Http\Request  $request
     * @throws Illuminate\Http\Response
     * @return Illuminate\Http\Response|void
     */
    private function validateYear($year, Request $request)
    {
        $this->authorize('create', BoardApp::class);

        if (! is_numeric($year)) {
            return redirect()->route('board.index');
        }

        $app = $request->user()->board_apps()->where('year', $year)->first();
        if (! $app) {
            return redirect()->route('board.index');
        }

        $this->authorize('view', $app);

        return $app;
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
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        $important_fields = ['bio', 'pronouns', 'hometown', 'major'];
        $missing_fields = collect($important_fields)->filter(function($field) use ($request) {
            return $request->user()->{$field} == null;
        });

        $logistics_needed = collect($app->interview_schedule)->values()->unique()->count() == 1;
        $common_needed = collect($app->common)->filter(function($item) { return empty($item); })->count();

        return view('board.app', compact('app', 'missing_fields', 'logistics_needed', 'common_needed'));
    }

    /**
     * Return the logistics view.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function logistics($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $dates = [];
        foreach($interview_options as $option) {
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                $dates[] = $time->format('D, M j, g:i a');
                $time->addMinutes(15);
            }
        }

        return view('board.pages.logistics', compact('app', 'dates'));
    }
}
