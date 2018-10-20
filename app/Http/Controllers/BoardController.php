<?php

namespace KRLX\Http\Controllers;

use KRLX\User;

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
}
