<?php

namespace KRLX\Http\Controllers;

use KRLX\Show;
use KRLX\Term;
use KRLX\Boost;
use Illuminate\Http\Request;

class BoostController extends Controller
{
    /**
     * Display the user's upgrade certificates.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $boosts = $request->user()->eligibleBoosts();

        return view('boost.index', compact('boosts'));
    }

    /**
     * Display the redemption options for a given certificate.
     *
     * @param  KRLX\Boost  $boost
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function redeem(Boost $boost, Request $request)
    {
        $this->authorize('redeem', $boost);

        $term = Term::orderByDesc('on_air')->first();
        $candidates = $request->user()->shows()->with(['hosts', 'track' => function ($query) {
            $query->where('boostable', true);
        }])->where('term_id', $term->id)->get()->sortBy('track_id');

        $shows = $candidates->filter(function ($show) use ($boost) {
            if ($boost->type == 'zone') {
                return true;
            }

            return $show->boosts()->where([['type', $boost->type], ['id', '<>', $boost->id]])->count() == 0;
        });

        return view('boost.redeem', compact('boost', 'shows'));
    }

    /**
     * Redeems a certificate by attaching it to a show.
     *
     * @param  KRLX\Boost  $boost
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function redeemToShow(Boost $boost, Request $request)
    {
        $this->authorize('redeem', $boost);

        $request->validate([
            'show_id' => ['required', 'string', 'exists:shows,id', function ($attribute, $value, $fail) use ($boost, $request) {
                $show = Show::find($value);
                if (! $show->hosts->contains($request->user())) {
                    $fail('You are not a host of this show.');
                } elseif ($boost->type != 'zone' and $show->boosts()->where('type', $boost->type)->count() > 0) {
                    $fail('This show already has a '.config('defaults.boosts.'.$boost->type).'.');
                } elseif (! $show->track->boostable) {
                    $fail('This show is not eligible for priority upgrades.');
                } elseif ($boost->term_id and $show->term_id != $boost->term_id) {
                    $fail('This upgrade certificate must be used on shows in a different term.');
                }
            }],
        ]);

        $boost->show_id = $request->input('show_id');
        $boost->save();

        return redirect()->route('home');
    }

    /**
     * Displays all upgrade certificates and which shows they're attached to.
     * @return Illuminate\Http\Response
     */
    public function master()
    {
        $boosts = Boost::orderByDesc('id')->get();

        return view('boost.master', compact('boosts'));
    }
}
