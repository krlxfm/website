<?php

namespace KRLX\Http\Controllers\API;

use Illuminate\Http\Request;
use KRLX\Http\Controllers\Controller;
use KRLX\Http\Requests\TermUpdateRequest;
use KRLX\Term;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Term::all();
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
            'id' => 'required|string|max:30|regex:/^[0-9]{4,}-[A-Z0-9][A-Z0-9_]*[A-Z0-9]$/',
            'on_air' => 'required|date',
            'off_air' => 'required|date|after:on_air',
            'boosted' => 'sometimes|boolean',
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
        return $term;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function update(TermUpdateRequest $request, Term $term)
    {
        foreach ($request->validated() as $field => $value) {
            $term->{$field} = $value;
        }
        $term->save();

        return $term;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\Term  $term
     * @return \Illuminate\Http\Response
     */
    public function destroy(Term $term)
    {
        $term->delete();

        return response(null, 204);
    }
}
