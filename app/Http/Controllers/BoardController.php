<?php

namespace KRLX\Http\Controllers;

use KRLX\User;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /**
     * Display the "Meet the Board" view.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $board = User::role('board')->orderBy('email')->get();

        return view('board.meet', compact('board'));
    }
}