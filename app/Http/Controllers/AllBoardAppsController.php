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

    public function pdf(BoardApp $app, Request $request)
    {
        $my_app = $request->user()->board_apps()->where('year', $app->year)->first();
        $redacted_sections = collect();
        if ($my_app and $my_app->id !== $app->id) {
            $my_positions = collect($my_app->positions->pluck('position'))->pluck('id');
            $redacted_sections = $app->positions->filter(function ($pos) use ($my_positions) {
                return $my_positions->contains($pos->position_id);
            })->pluck('position')->pluck('id');
        }

        return view('board.all.pdf', compact('app', 'redacted_sections'));
    }
}
