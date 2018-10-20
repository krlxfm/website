<?php

namespace KRLX\Http\Controllers;

use KRLX\User;
use KRLX\BoardApp;
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
        return redirect()->route('board.index');
    }
}
