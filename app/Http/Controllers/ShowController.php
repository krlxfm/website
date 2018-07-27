<?php

namespace KRLX\Http\Controllers;

use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'title' => 'required|string|min:3',
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
        return view('shows.content', compact('show'));
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

        $shows = $term->shows->sort(function ($a, $b) {
            $boost_diff = ($b->boost == 'S') <=> ($a->boost == 'S');
            $track_diff = $a->track->order <=> $b->track->order;
            $priority_diff = $a->priority <=> $b->priority;
            $updated_at_diff = $a->updated_at <=> $b->updated_at;
            $id_diff = $a->id <=> $b->id;

            $diffs = [$boost_diff, $track_diff, $priority_diff, $updated_at_diff, $id_diff];

            foreach ($diffs as $diff) {
                if ($diff != 0) {
                    return $diff;
                }
            }
        });

        return view('shows.all', compact('shows', 'terms', 'term'));
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
}
