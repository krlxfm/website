<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Term;
use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            'id' => 'required|string|max:30|regex:/^[0-9]{4,}-[A-Z][A-Z_]*[A-Z]$/',
            'on_air' => 'required|date',
            'off_air' => 'required|date|after:on_air'
        ]);

        $term = Term::create($request->all());
        return $term;
    }

    /**
     * Display the specified resource.
     *
     * @param  \KRLX\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function show(Term $term)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Term $term)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function destroy(Term $term)
    {
        //
    }
}
