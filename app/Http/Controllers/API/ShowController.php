<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use KRLX\Http\Controllers\Controller;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Show::with(['hosts', 'invitees'])->get();
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
            'title' => 'sometimes|string|min:3',
            'track_id' => 'required|integer|exists:tracks,id',
            'term_id' => [
                'required',
                'string',
                Rule::exists('terms', 'id')->where(function ($query) {
                    $query->where('accepting_applications', true);
                })
            ],
            'source' => 'sometimes|string|min:3|regex:[A-Za-z][A-Za-z0-9-_\./:]+'
        ]);

        $show = Show::create($request->all());
        return $show;
    }

    /**
     * Display the specified resource.
     *
     * @param  \KRLX\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function show(Show $show)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Show $show)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function destroy(Show $show)
    {
        //
    }
}