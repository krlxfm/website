<?php

namespace KRLX\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use KRLX\BoardApp;
use KRLX\Http\Controllers\API\FeedController;
use KRLX\Term;

class HomeController extends Controller
{
    /**
     * Show the application landing page.
     *
     * @param  Illuminuate\Http\Request
     * @return Illuminate\Http\Response
     */
    public function welcome(Request $request)
    {
        $feed = new FeedController;
        $show = $feed->now();
        $transition = Carbon::parse($show->end)->format('g:i a');

        $messages = collect(config('messages.public'));
        if (starts_with($request->ip(), '137.22.') or $request->ip() == '::1') {
            $messages = $messages->concat(config('messages.carleton'));
        }

        return view('welcome', compact('show', 'transition', 'messages'));
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

        $board_app = BoardApp::where([['user_id', $user->id], ['year', date('Y')]])->first();

        return view('home', compact('user', 'shows', 'term', 'board_app'));
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
            return redirect()->intended('/profile');
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
        $rules = $this->getValidationRules();
        $request->validate($rules);
        $request->validate($this->getPronounValidationRules());

        $user = $request->user();
        foreach (array_keys($rules) as $field) {
            if ($field == 'status' or $field == 'source') {
                continue;
            }
            $user->{$field} = $request->input($field);
        }
        $statuses = ['faculty' => 1, 'staff' => 2, 'student' => $request->input('year')];
        $user->year = $statuses[$request->input('status')];

        if (! ends_with($user->email, '@carleton.edu')) {
            $user->year = 3;
        }

        $pronoun_fields = ['pronouns-he', 'pronouns-she', 'pronouns-they', 'other_pronouns'];
        $pronouns = [];
        foreach ($pronoun_fields as $field) {
            if ($request->has($field) and strlen($request->{$field}) > 0) {
                $pronouns[] = str_replace('-', '/', $request->{$field});
            }
        }
        $user->pronouns = implode(', ', collect($pronouns)->unique()->all());

        $user->save();

        return redirect()->intended('/home')->with('status', $request->has('source') ? 'Your profile has been updated!' : 'Your account has been activated!');
    }

    /**
     * Get the rules required to save an onboarding request.
     *
     * @return array
     */
    private function getValidationRules()
    {
        return [
            'name' => 'required|string',
            'first_name' => 'required|string',
            'phone_number' => 'required|string|min:10',
            'status' => 'required|in:student,faculty,staff',
            'year' => 'required_if:status,student|nullable|integer|min:1900|max:'.(date('Y') + 5),
            'major' => 'sometimes|present|max:190',
            'hometown' => 'sometimes|present|max:190',
            'bio' => 'sometimes|present|max:65000',
            'favorite_music' => 'sometimes|present|max:65000',
            'favorite_shows' => 'sometimes|present|max:65000',
            'source' => 'sometimes|present|string',
            'walkup' => 'sometimes|present|max:190',
        ];
    }

    /**
     * Private function to validate pronouns input, becuase that's hard.
     *
     * @return array
     */
    private function getPronounValidationRules()
    {
        return [
            'pronouns-he' => 'sometimes|in:he-him-his',
            'pronouns-she' => 'sometimes|in:she-her-hers',
            'pronouns-they' => 'sometimes|in:they-them-their',
            'other_pronouns_checkbox' => 'sometimes|in:other',
            'other_pronouns' => 'required_with:other_pronouns_checkbox|nullable|string|regex:/^[^,]+$/',
        ];
    }
}
