<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Track;
use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;
use KRLX\Http\Requests\TrackUdpateRequest;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Track::all();
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
            'name' => 'required|min:3|max:190|unique:tracks',
            'description' => 'required|min:20',
        ]);

        $track = Track::create($request->all());

        return $track;
    }

    /**
     * Display the specified resource.
     *
     * @param  \KRLX\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function show(Track $track)
    {
        return $track;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function update(TrackUdpateRequest $request, Track $track)
    {
        foreach ($request->validated() as $field => $value) {
            $track->{$field} = $value;
        }
        $track->save();

        return $track;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\Track  $track
     * @return \Illuminate\Http\Response
     */
    public function destroy(Track $track)
    {
        $track->delete();

        return response(null, 204);
    }
}
