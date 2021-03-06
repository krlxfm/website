<?php

namespace KRLX\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use KRLX\BoardApp;
use KRLX\Config;
use KRLX\Mail\BoardInterview;

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
                $interviews_without_null = $interviews->values()->reject(function ($i) {
                    return $i === null;
                });
                if (BoardApp::whereIn('id', $interviews->keys()->all())->count() !== $interviews->count()) {
                    $fail("The $attribute array contains invalid board application IDs.");
                } elseif ($interviews_without_null->unique()->count() !== $interviews_without_null->count()) {
                    $fail('Two or more candidates have been scheduled at the same time.');
                }
            }],
            'interviews.*' => 'nullable|date',
            'notify' => 'required|boolean',
        ]);

        foreach ($request->input('interviews') as $key => $time) {
            $app = BoardApp::find($key);
            $app->interview = $time;
            $app->save();
        }

        if ($request->input('notify')) {
            $this->sendCandidateNotifications($request->input('interviews'));
            $request->session()->flash('status', 'Interview times have been saved, and candidates have been notified via email of their times.');
        } else {
            $request->session()->flash('status', 'Interview times have been updated. Candidates have not been notified yet; click "Save and Notify Candidates" to send emails.');
        }

        return redirect()->route('board.interviews');
    }

    public function schedulePDF()
    {
        $apps = BoardApp::where([['year', date('Y')], ['submitted', true]])->whereNotNull('interview')->get()->sortBy('interview');

        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $dates = [];
        foreach ($interview_options as $option) {
            if (! array_key_exists($option['date'], $dates)) {
                $dates[$option['date']] = [];
            }
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                $dates[$option['date']][] = $time->copy();
                $time->addMinutes(15);
            }
        }

        return view('board.all.schedule', compact('apps', 'dates'));
    }

    private function sendCandidateNotifications(array $candidates)
    {
        foreach ($candidates as $app_id => $time) {
            if ($time === null) {
                continue;
            }
            $app = BoardApp::find($app_id);
            Mail::to($app->user)->queue(new BoardInterview($app));
        }
    }
}
