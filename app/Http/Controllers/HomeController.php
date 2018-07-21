<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $terms = Term::orderByDesc('on_air')->get();
        $term = $terms->first();

        $user = $request->user();
        $shows = $user->shows()->where('term_id', $term->id)->get();
        return view('home', compact('user', 'shows', 'term'));
    }
}
