<?php

namespace KRLX\Http\Controllers;

use KRLX\Track;
use KRLX\Term;
use KRLX\Show;
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
        if($term == null) {
            $term = $terms->first();
        }

        $incomplete_shows = $request->user()->shows()->where([
            ['submitted', '=', false],
            ['term_id', '=', $term->id]
        ])->orderByDesc('id')->get();
        $completed_shows = $request->user()->shows()->where([
            ['submitted', '=', true],
            ['term_id', '=', $term->id]
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
            'title' => 'required|string|min:3'
        ]);

        $show = Show::create(array_merge($request->all(), ['source' => 'web']));
        $show->hosts()->attach($request->user(), ['accepted' => true]);
        return redirect()->route('shows.participants', $show);
    }
}
