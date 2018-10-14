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
    public function index()
    {
        $board = User::role('board')->orderBy('email')->orderBy('order')->get();

        return view('board.meet', compact('board'));
    }
}
