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

        $app = BoardApp::find($request->input('board_app_id'));
        $this->authorize('update', $app);

        $position = $app->positions()->create(['position_id' => $request->input('position_id'), 'order' => $app->positions->count()]);

        return redirect()->route('positions.show', $position);
    }

    /**
     * Display the specified resource.
     *
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function show(PositionApp $position)
    {
        $pos = $position->position;
        $app = $position->board_app;

        $this->authorize('update', $app);

        return view('board.pages.position', compact('pos', 'app', 'position'));
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
        $correct = 0;
        foreach(array_keys($request->input('responses')) as $question) {
            if (in_array($question, $position->position->app_questions)) {
                $correct += 1;
            }
        };

        $request->validate([
            'responses' => ['sometimes','array','size:'.count($position->position->app_questions), function ($attribute, $value, $fail) use ($position, $correct) {
                if ($correct !== count($position->position->app_questions)) {
                    $fail("One or more questions are missing.");
                }
            }]
        ]);
        $values = $request->all();

        // STOP: This is a MAJOR XSS vulnerability, so we need to block all
        // <script> tags from getting through.
        $this->sanitizeInput($values);
        $position->responses = $request->input('responses');
        $position->save();

        return redirect()->route('board.app', $position->board_app->year);
    }

    /**
     * Sanitize inputs which could potentially contain XSS code.
     *
     * @param  array  $input
     * @return array
     */
    private function sanitizeInput(array &$input)
    {
        foreach($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->sanitizeInput($value);
            } else {
                $value = str_replace('<script>', '&lt;script&gt;', $value);
            }
        }

        return $input;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\PositionApp  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(PositionApp $position)
    {
        $pos = $position->position;
        $app = $position->board_app;

        $this->authorize('update', $app);

        $order = $position->order;
        $positions_after = $app->positions->filter(function($item) use ($order) {
            return $item->order > $order;
        });
        foreach($positions_after as $pos_after) {
            $pos_after->order -= 1;
            $pos_after->save();
        }
        $position->delete();

        return redirect()->route('board.app', $app->year);
    }
}
