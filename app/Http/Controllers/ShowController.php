<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;
use KRLX\Show;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * Display the user's shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Term|null  $term
     * @return void
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
        ])->get();
        $completed_shows = $request->user()->shows()->where([
            ['submitted', '=', true],
            ['term_id', '=', $term->id]
        ])->get();
        $invitations = $request->user()->invitations()->where('term_id', $term->id)->get();

        return view('shows.my', compact('term', 'terms', 'invitations', 'incomplete_shows', 'completed_shows'));
    }
}
