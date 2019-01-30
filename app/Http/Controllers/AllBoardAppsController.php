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
        $apps = BoardApp::where([['year', date('Y')], ['submitted', true]])->with('user')->get();
        if ($request->user()->can('view incomplete board applications')) {
            $apps = $apps->concat(BoardApp::where([['year', date('Y')], ['submitted', false]])->with('user')->get());
        }
        $apps = $apps->sortBy('user.email');
        $my_app = $request->user()->board_apps()->where('year', date('Y'))->first();

        return view('board.all.index', compact('apps', 'my_app'));
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

    public function saveInterviews(Request $request)
    {
        $request->validate([
            'interviews' => ['array', function ($attribute, $value, $fail) {
                // Custom validation logic to check that all IDs are valid, and no duplicate interviews.
                $interviews = collect($value);
                $interviews_without_null = $interviews->reject(function ($i) {
                    return $i === null;
                });
                if (BoardApp::whereIn('id', $interviews->keys()->all())->count() !== $interviews->count()) {
                    $fail("The $attribute array contains invalid board application IDs.");
                } elseif ($interviews_without_null->unique()->count() !== $interviews_without_null->count()) {
                    $fail('Two or more candidates have been scheduled at the same time.');
                }
            }],
            'interviews.*' => 'nullable|date',
        ]);

        foreach ($request->input('interviews') as $key => $time) {
            $app = BoardApp::find($key);
            $app->interview = $time;
            $app->save();
        }

        $request->session()->flash('status', 'Interview times have been updated.');

        return redirect()->route('board.interviews');
    }
}
