<?php

namespace KRLX\Http\Controllers;

use KRLX\Show;
use KRLX\Term;
use Validator;
use KRLX\Track;
use KRLX\Rules\Profanity;
use Illuminate\Http\Request;
use KRLX\Rulesets\ShowRuleset;
use Illuminate\Validation\Rule;
use KRLX\Http\Controllers\API\ShowController as APIController;

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
     * @return Illuminate\Http\Response
     */
    public function create()
    {
        $terms = Term::where('accepting_applications', true)->orderByDesc('on_air')->get();
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
                $query->where('accepting_applications', true);
            })],
            'title' => ['required', 'string', 'min:3', 'max:200', new Profanity],
        ]);

        $show = Show::create(array_merge($request->all(), ['source' => 'web']));
        $show->hosts()->attach($request->user(), ['accepted' => true]);

        return redirect()->route('shows.hosts', $show);
    }

    /**
     * Display the hosts (and invitees) of a show.
     *
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function hosts(Show $show)
    {
        return view('shows.hosts', compact('show'));
    }

    /**
     * Display the content fields of a show.
     *
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function content(Show $show)
    {
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
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function schedule(Show $show)
    {
        return view('shows.schedule', compact('show'));
    }

    /**
     * Display the scheduling fields of a show.
     *
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function review(Show $show)
    {
        return view('shows.review', compact('show'));
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

        $shows = $term->shows()->with('track')->whereHas('track', function ($query) {
            $query->where('order', '>', 0);
        })->get()->sort(function ($a, $b) {
            return $this->sortShows($a, $b);
        });

        $one_off_shows = $term->shows()->with('track')->whereHas('track', function ($query) {
            $query->where('order', 0);
        })->get()->groupBy('track.id')->transform(function ($track) {
            return $track->sort(function ($a, $b) {
                return $this->sortShows($a, $b);
            });
        });

        return view('shows.all', compact('shows', 'terms', 'term', 'one_off_shows'));
    }

    /**
     * Function to sort two shows by priority.
     *
     * @param  KRLX\Show  $show_a
     * @param  KRLX\Show  $show_b
     * @return int
     */
    private function sortShows(Show $show_a, Show $show_b)
    {
        $boost_diff = ($show_b->boost == 'S') <=> ($show_a->boost == 'S');
        $track_diff = $show_a->track->order <=> $show_b->track->order;
        $priority_diff = $show_a->priority <=> $show_b->priority;
        $completed_diff = $show_b->submitted <=> $show_a->submitted;
        $updated_at_diff = $show_a->updated_at <=> $show_b->updated_at;
        $id_diff = $show_a->id <=> $show_b->id;

        $diffs = [$boost_diff, $track_diff, $priority_diff, $completed_diff, $updated_at_diff, $id_diff];

        foreach ($diffs as $diff) {
            if ($diff != 0) {
                return $diff;
            }
        }
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
        $terms = Term::orderByDesc('on_air')->get();
        if ($term == null) {
            $term = $terms->first();
        }

        $hosts = $term->shows()->where('submitted', true)->get()->pluck('hosts')->flatten();

        $users = $hosts->unique(function ($user) {
            return $user['id'];
        })->sortBy('email');

        return view('shows.djs', compact('term', 'terms', 'users'));
    }

    /**
     * Display the "Join Show" view.
     *
     * @param  KRLX\Show|null  $show
     * @return Illuminate\Http\Response
     */
    public function join(Show $show = null)
    {
        if ($show == null) {
            return view('shows.find');
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
