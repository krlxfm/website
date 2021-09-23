<?php

namespace KRLX\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use KRLX\Http\Controllers\API\ShowController as APIController;
use KRLX\Rules\Profanity;
use KRLX\Rulesets\ShowRuleset;
use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use KRLX\User;
use Validator;

class ShowController extends Controller
{
    /**
     * Display the user's shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Term|null  $term
     * @return Illuminate\Http\Response
     */
    public function my(Request $request, Term $term = null)
    {
        $terms = Term::orderByDesc('on_air')->get();
        if ($term == null) {
            $term = $terms->first();
        }

        $incomplete_shows = $request->user()->shows()->where([
            ['submitted', '=', false],
            ['term_id', '=', $term->id],
        ])->orderByDesc('id')->get();
        $completed_shows = $request->user()->shows()->where([
            ['submitted', '=', true],
            ['term_id', '=', $term->id],
        ])->orderByDesc('id')->get();
        $invitations = $request->user()->invitations()->where('term_id', $term->id)->get();

        return view('shows.my', compact('term', 'terms', 'invitations', 'incomplete_shows', 'completed_shows'));
    }

    /**
     * Returns view for users to select a track and create a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $terms = Term::where('status', 'active')->get();
        if ($request->user()->hasPermissionTo('override pending term')) {
            $terms = $terms->concat(Term::where('status', 'pending')->get());
        }
        if ($request->user()->hasPermissionTo('override closed term')) {
            $terms = $terms->concat(Term::where('status', 'closed')->get());
        }
        $terms = $terms->sortByDesc('on_air');
        $tracks = Track::where('active', true)->get();

        return view('shows.create', compact('terms', 'tracks'));
    }

    /**
     * Create a new show and place it in storage.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'track_id' => ['required', 'integer', Rule::exists('tracks', 'id')->where(function ($query) {
                $query->where('active', true);
            })],
            'term_id' => ['required', 'string', Rule::exists('terms', 'id')->where(function ($query) {
                $query->whereIn('status', ['active', 'pending', 'closed']);
            })],
            'title' => ['required', 'string', 'min:3', 'max:200', new Profanity],
        ]);

        $term = Term::find($request->input('term_id'));
        $this->authorize('createShows', $term);

        $controller = new APIController();
        $show = Show::create(array_merge($request->all(), ['source' => 'web']));
        Log::info("Show {$show->id} created.", [$request->all()]);
        $show->hosts()->attach($request->user(), ['accepted' => true]);

        $controller->generateCertificate($request->user(), $show, $term);

        return redirect()->route('shows.hosts', $show);
    }

    /**
     * Display the hosts (and invitees) of a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function hosts(Request $request, Show $show)
    {
        if (! $request->user()->can('view', $show)) {
            return redirect()->route('shows.join', $show);
        }

        return view('shows.hosts', compact('show'));
    }

    /**
     * Display the content fields of a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function content(Request $request, Show $show)
    {
        if (! $request->user()->can('view', $show)) {
            return redirect()->route('shows.join', $show);
        }

        $ruleset = new ShowRuleset($show, []);
        $rules = collect($ruleset->rules(true));
        $keys = array_merge(['title', 'description', 'content'], $rules->filter(function ($value, $key) {
            return starts_with($key, 'content.');
        })->keys()->all());

        $validator = Validator::make($show->toArray(), $rules->only($keys)->all());
        $initialErrors = $validator->errors()->messages();

        return view('shows.content', compact('show', 'initialErrors'));
    }

    /**
     * Display the scheduling fields of a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function schedule(Request $request, Show $show)
    {
        if (! $request->user()->can('view', $show)) {
            return redirect()->route('shows.join', $show);
        }

        return view('shows.schedule', compact('show'));
    }

    /**
     * Display ALL fields of a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function review(Request $request, Show $show)
    {
        if (! $request->user()->can('view', $show)) {
            return redirect()->route('shows.join', $show);
        }

        return view('shows.review', compact('show'));
    }

    /**
     * Display the view to delete a show.
     *
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function delete(Show $show)
    {
        $this->authorize('delete', $show);

        return view('shows.delete', compact('show'));
    }

    /**
     * Destroy a show.
     *
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function destroy(Show $show)
    {
        $this->authorize('delete', $show);

        $show->delete();

        return redirect()->route('shows.my');
    }

    /**
     * Display the master list of shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Term|null  $term
     * @return Illuminate\Http\Response
     */
    public function all(Request $request, Term $term = null)
    {
        $terms = Term::orderByDesc('on_air')->get();
        if ($term == null) {
            $term = $terms->first();
        }

        $shows = $term->showsInPriorityOrder(true);
        $one_off_shows = $term->showsInPriorityOrder(false)->groupBy('track.id');

        return view('shows.all', compact('shows', 'terms', 'term', 'one_off_shows'));
    }

    /**
     * Download a CSV of all shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Term|null  $term
     * @return Illuminate\Http\Response
     */
    public function download(Request $request, Term $term = null)
    {
        if ($term == null) {
            $terms = Term::orderByDesc('on_air')->get();
            $term = $terms->first();
        }

        $shows = $term->showsInPriorityOrder(true)->where('submitted', true);

        $file = fopen(storage_path('app/shows.csv'), 'w');
        fputcsv($file, ['id', 'title', 'djs', 'day', 'start', 'end', 'flags']);
        foreach ($shows as $show) {
            fputcsv($file, [
                'id' => $show->id,
                'title' => $show->title,
                'djs' => implode(', ', $show->hosts->pluck('full_name')->all()),
                'day' => $show->day,
                'start' => $show->start,
                'end' => $show->end,
                'flags' => ($show->priority->terms == 0 ? 'SHADOWING REQUIRED' : ''),
            ]);
        }
        fclose($file);

        return response()->download(storage_path('app/shows.csv'))->deleteFileAfterSend(true);
    }

    /**
     * Download a CSV of all shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Term|null  $term
     * @return Illuminate\Http\Response
     */
    public function downloadRoster(Request $request, Term $term = null)
    {
        if ($term == null) {
            $terms = Term::orderByDesc('on_air')->get();
            $term = $terms->first();
        }

        $hosts = User::orderBy('email')->with(['points', 'shows' => function ($query) use ($term) {
            return $query->where([['term_id', $term->id], ['submitted', true]]);
        }])->get();

        $users = $hosts->filter(function ($user) {
            return $user->shows->count() > 0;
        });

        $file = fopen(storage_path('app/djs.csv'), 'w');
        fputcsv($file, ['email', 'name', 'phone', 'terms', 'year', 'flags']);
        foreach ($users as $user) {
            $flags = [];
            if ($user->priorityAsOf($term->id)->terms === 0) {
                $flags[] = 'TRAINING REQUIRED';
            }
            if ($user->year <= ((int) date('Y') - (int) $term->boosted)) {
                $flags[] = 'VERIFY CARD ACCESS';
            }
            fputcsv($file, [
                'email' => $user->email,
                'name' => $user->name,
                'phone' => $user->phone_number,
                'terms' => $user->priorityAsOf($term->id)->terms,
                'year' => $user->year,
                'flags' => implode(', ', $flags),
            ]);
        }
        fclose($file);

        return response()->download(storage_path('app/djs.csv'))->deleteFileAfterSend(true);
    }

    /**
     * Display the list of all DJs involved in at least one show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Term|null  $term
     * @return Illuminate\Http\Response
     */
    public function djs(Request $request, Term $term = null)
    {
        if ($term == null) {
            $terms = Term::orderByDesc('on_air')->get();
            $term = $terms->first();
        }

        $hosts = User::orderBy('email')->with(['points', 'shows' => function ($query) use ($term) {
            return $query->where([['term_id', $term->id], ['submitted', true]]);
        }])->get();

        $users = $hosts->filter(function ($user) {
            return $user->shows->count() > 0;
        });

        return view('shows.djs', compact('term', 'terms', 'users'));
    }

    /**
     * Display the "Join Show" view.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show|null  $show
     * @return Illuminate\Http\Response
     */
    public function join(Request $request, Show $show = null)
    {
        if ($show == null) {
            return view('shows.find');
        } elseif ($show->hosts->pluck('id')->contains($request->user()->id)) {
            return redirect()->route('shows.review', $show)->with('status', 'You are already a part of this show!');
        } else {
            return view('shows.join', compact('show'));
        }
    }

    /**
     * Process a join request.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show|null  $show
     * @return Illuminate\Http\Response
     */
    public function processJoinRequest(Request $request, Show $show)
    {
        $controller = new APIController();

        // Throws an HTTP 400 if a bad token comes in.
        $controller->join($request, $show);

        $request->session()->flash('success', "You have successfully joined {$show->title}! Please carefully review the schedule below and add your details if necessary.");

        return redirect()->route('shows.schedule', $show);
    }
}
