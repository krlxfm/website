<?php

namespace KRLX\Http\Controllers;

use Validator;
use KRLX\User;
use KRLX\Config;
use Carbon\Carbon;
use KRLX\BoardApp;
use KRLX\Position;
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
     * Display the "Positions" view.
     *
     * @return Illuminate\Http\Response
     */
    public function positions()
    {
        $positions = Position::where('active', true)->orderBy('order')->get();

        return view('board.positions', compact('positions'));
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

        $logistics_needed = collect($app->interview_schedule)->values()->sum() == 0;
        $common_needed = collect($app->common)->filter(function($item) { return empty($item); })->count();

        $positions = Position::where('active', true)->orderBy('order')->get()->reject(function($position) use ($request, $app) {
            return $app->positions->pluck('position_id')->contains($position->id) or ($position->restricted and $request->user()->cant('apply for Station Manager'));
        });

        $can_submit = ($missing_fields or $logistics_needed or $common_needed or $app->positions->count() == 0);
        if ($can_submit) {
            foreach($app->positions as $position) {
                if (!$position->complete()) $can_submit = false;
            }
        }

        return view('board.app', compact('app', 'missing_fields', 'logistics_needed', 'common_needed', 'positions', 'can_submit'));
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

        $dates = $this->interviewDates();

        return view('board.pages.logistics', compact('app', 'dates'));
    }

    /**
     * Return the common questions view.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function common($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        return view('board.pages.common', compact('app'));
    }

    /**
     * Submit the application.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function submit($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        $this->authorize('update', $app);
        $app->submitted = true;

        $next_year = $app->year + 1;
        $request->session()->flash('status', "Congratulations - your {$app->year}-$next_year Board application has been submitted! Look for an email in the next few days with information on the interview process.");
        return redirect()->route('board.index');
    }

    /**
     * Return the position-reordering view.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function reorder($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        return view('board.pages.reorder', compact('app'));
    }

    /**
     * Return the position-reordering view.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function storeReorder($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        $positions = $app->positions->pluck('position_id')->all();
        $validator = Validator::make(['order' => explode(',', $request->input('order'))], [
            'order' => 'required|array|min:1',
            'order.*' => 'integer|distinct|in:'.implode(',',$positions)
        ]);
        if ($validator->fails()) {
            return redirect(route('board.pages.reorder', $app->year))->withErrors($validator)->withInput();
        }
        $orders = explode(',', $request->input('order'));
        foreach ($app->positions as $position) {
            $position->order = array_search($position->position_id, $orders);
            $position->save();
        }

        return redirect()->route('board.app', $app->year);
    }

    /**
     * Store changes to the board application.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  int  $year
     * @return Illuminate\Http\Response
     */
    public function updateApplication($year, Request $request)
    {
        $app = $this->validateYear($year, $request);
        if (! $app instanceof BoardApp) {
            return $app;
        }

        $request->validate($this->validationRules($app));
        $values = $request->all();

        // STOP: This is a MAJOR XSS vulnerability, so we need to block all
        // <script> tags from getting through.
        $this->sanitizeInput($values);

        $app->fill($values);
        $app->save();

        return redirect()->route('board.app', $app->year);
    }

    /**
     * Sanitize inputs which could potentially contain XSS code.
     *
     * @param  array  $input
     * @return array
     */
    private function sanitizeInput(array &$input)
    {
        foreach($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->sanitizeInput($value);
            } else {
                $value = str_replace('<script>', '&lt;script&gt;', $value);
            }
        }

        return $input;
    }

    /**
     * Get the validation rules for a Board application.
     *
     * @return array
     */
    private function validationRules(BoardApp $app)
    {
        $dates = collect($this->interviewDates());
        $rules = [
            'interview_schedule' => ['sometimes','array','size:'.$dates->count(), function ($attribute, $value, $fail) use ($dates) {
                foreach($dates->all() as $date) {
                    if (! array_key_exists($date->format('Y-m-d H:i:s'), $value)) {
                        $fail("Please enter your availability for {$date->format('D, M j, g:i a')}.");
                    }
                }
            }],
            'interview_schedule.*' => 'integer|between:1,3',
            'ocs' => 'sometimes|in:none,abroad_fa,abroad_sp,abroad_wi',
            'remote' => 'sometimes|boolean',
            'remote_contact' => 'sometimes|required_if:remote,1',
            'remote_platform' => 'required_if:remote,1',
            'common' => ['sometimes','array','size:'.count($app->common), function ($attribute, $value, $fail) use ($app) {
                foreach(collect($app->common)->keys()->all() as $key) {
                    if (! array_key_exists($key, $value)) {
                        $fail("The question $key is not present in the Common answers.");
                    }
                }
            }],
        ];

        return $rules;
    }

    /**
     * Get the interview dates available for Board applications.
     *
     * @return array
     */
    private function interviewDates()
    {
        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $opts = [];
        foreach($interview_options as $option) {
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                $opts[] = $time->copy();
                $time->addMinutes(15);
            }
        }
        return $opts;
    }
}
