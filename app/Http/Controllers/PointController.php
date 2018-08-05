<?php

namespace KRLX\Http\Controllers;

use Parsedown;
use KRLX\Term;
use Illuminate\Http\Request;

class PointController extends Controller
{
    /**
     * View the membership contract.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function contract(Request $request)
    {
        $parsedown = new Parsedown();
        $contract = $parsedown->text(file_get_contents(resource_path('assets/markdown/contract.md')));

        if($request->session()->has('term')) {
            $term = Term::find($request->session()->get('term'));
        } else {
            $term = Term::orderByDesc('on_air')->get()->first();
        }

        return view('legal.contract', compact('contract', 'term'));
    }
}
