<?php

namespace KRLX\Http\Controllers;

use KRLX\Term;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $terms = Term::orderByDesc('on_air')->get();
        $term = $terms->first();

        $user = $request->user();
        $shows = $user->shows()->where('term_id', $term->id)->get();
        $boosts = $user->eligibleBoosts();

        return view('home', compact('user', 'shows', 'term', 'boosts'));
    }

    /**
     * Show the screen for onboarding users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function onboard(Request $request)
    {
        $user = $request->user();
        if (! ends_with($user->email, '@carleton.edu') or ! empty($user->phone_number)) {
            return redirect()->intended('/home');
        }

        return view('legal.onboard');
    }

    /**
     * Store onboarding requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOnboarding(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'first_name' => 'required|string',
            'phone_number' => 'required|string|min:10',
            'status' => 'required|in:student,faculty,staff',
            'year' => 'required_if:status,student|nullable|integer|min:1900|max:'.(date('Y') + 5),
            'major' => 'present|max:190',
            'hometown' => 'present|max:190',
            'bio' => 'present|max:65000',
            'favorite_music' => 'present|max:65000',
            'favorite_shows' => 'present|max:65000',
        ];
        $request->validate($rules);

        $user = $request->user();
        foreach (array_keys($rules) as $field) {
            if ($field == 'status') {
                continue;
            }
            $user->{$field} = $request->input($field);
        }
        $status = $request->input('status');
        if ($status == 'faculty') {
            $user->year = 1;
        } elseif ($status == 'staff') {
            $user->year = 2;
        }
        $user->save();

        return redirect()->intended('/home');
    }
}
