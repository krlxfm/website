<?php

namespace KRLX\Http\Controllers;

use KRLX\BoardApp;
use KRLX\PositionApp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'position_id' => ['required', 'integer', Rule::exists('positions', 'id')->where(function ($query) {
                $query->where('active', true);
            })],
            'board_app_id' => ['required', 'integer', Rule::exists('board_apps', 'id')->where(function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })],
        ]);

        $this->authorize('create', PositionApp::class);

        $app = BoardApp::find($request->input('board_app_id'));
        $position = $app->positions()->create(['position_id' => $request->input('position_id'), 'order' => $app->positions->count()]);

        return redirect()->route('positions.edit', $position);
    }

    /**
     * Display the specified resource.
     *
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function show(PositionApp $position)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function edit(PositionApp $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PositionApp $position)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(PositionApp $position)
    {
        //
    }
}
