<?php

namespace KRLX\Http\Controllers;

use KRLX\BoardApp;
use Illuminate\Http\Request;

class AllBoardAppsController extends Controller
{
    public function index(Request $request)
    {
        $apps = BoardApp::where([['year', date('Y')], ['submitted', true]])->get();
        $my_app = $request->user()->board_apps()->where('year', date('Y'))->first();

        return view('board.all.index', compact('apps', 'my_app'));
    }
}
