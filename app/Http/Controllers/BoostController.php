<?php

namespace KRLX\Http\Controllers;

use Illuminate\Http\Request;

class BoostController extends Controller
{
    /**
     * Display the user's upgrade certificates.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $boosts = $request->user()->boosts()->with('show')->get();
        return view('boost.index', compact('boosts'));
    }
}
