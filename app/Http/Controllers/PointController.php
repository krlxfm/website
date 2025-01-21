<?php

namespace KRLX\Http\Controllers;

use Illuminate\Http\Request;
use KRLX\Term;
use Parsedown;

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

        if ($request->session()->has('term')) {
            $term = Term::find($request->session()->get('term'));
        } else {
            $term = Term::orderByDesc('on_air')->get()->first();
        }

        return view('legal.contract', compact('contract', 'term'));
    }

    public function contractnoform(Request $request)
    {
        $parsedown = new Parsedown();
        $contract = $parsedown->text(file_get_contents(resource_path('assets/markdown/contract.md')));

        if ($request->session()->has('term')) {
            $term = Term::find($request->session()->get('term'));
        } else {
            $term = Term::orderByDesc('on_air')->get()->first();
        }

        return view('legal.contractnoform', compact('contract', 'term'));
    }

    /**
     * Sign the contract for a given term.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function sign(Request $request)
    {
        $request->validate([
            'contract' => 'accepted',
            'drop_policy' => 'accepted',
            'rescheduling_policy' => 'accepted',
            'phone_number_verification' => 'accepted',
            'term' => 'required|string|exists:terms,id',
        ]);

        if ($request->user()->points()->where('term_id', $request->input('term'))->count() == 0) {
            $request->user()->points()->create([
                'term_id' => $request->input('term'),
                'status' => 'provisioned',
            ]);
        }

        return redirect()->intended('/home');
    }
}
