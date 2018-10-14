<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use Validator;
use KRLX\Mail\ShowReminder;
use Illuminate\Http\Request;
use KRLX\Mail\ShowSubmitted;
use KRLX\Rulesets\ShowRuleset;
use Illuminate\Validation\Rule;
use KRLX\Mail\NewUserInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KRLX\Http\Controllers\Controller;
use KRLX\Notifications\ShowInvitation;
use KRLX\Http\Requests\ShowUpdateRequest;
use KRLX\Http\Resources\Show as ShowResource;
use Illuminate\Contracts\Encryption\DecryptException;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()->shows()->with(['hosts', 'invitees'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Show::class);
        $request->validate([
            'title' => 'sometimes|string|min:3',
            'track_id' => 'required|integer|exists:tracks,id',
            'term_id' => [
                'required',
                'string',
                Rule::exists('terms', 'id')->where(function ($query) {
                    $query->whereIn('status', ['active', 'pending', 'closed']);
                }),
            ],
            'source' => 'sometimes|string|min:3|regex:[A-Za-z][A-Za-z0-9-_\./:]+',
        ]);

        $term = Term::find($request->input('term_id'));
        $this->authorize('createShows', $term);

        $show = $request->user()->shows()->create($request->all(), ['accepted' => true]);
        $this->generateCertificate($request->user(), $show, $term);

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
        $this->authorize('basicView', $show);

        if (Auth::user()->can('view', $show)) {
            return $show->with(['hosts', 'invitees'])->first();
        }

        return new ShowResource($show);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KRLX\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function update(ShowUpdateRequest $request, Show $show)
    {
        $this->authorize('update', $show);

        foreach ($request->validated() as $field => $value) {
            $show->{$field} = $value;
        }
        $show->save();

        return $this->show($show);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KRLX\Show  $show
     * @return \Illuminate\Http\Response
     */
    public function destroy(Show $show)
    {
        $this->authorize('delete', $show);
        $show->delete();

        return response(null, 204);
    }

    /**
     * Invite a host who does not have a user account.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function inviteHostWithoutUserAccount(Request $request, Show $show)
    {
        $this->authorize('update', $show);
        $request->validate([
            'invite' => 'array',
            'invite.*' => 'email|distinct',
        ]);

        foreach ($request->input('invite') as $new_email) {
            $host = User::where('email', $new_email)->first();

            if (! $host) {
                // Send the new user an email invitation.
                Mail::to($new_email)->queue(new NewUserInvitation($show, $request->user()));
            }
        }

        return $show;
    }

    /**
     * Manage the hosts of a show.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function changeHosts(Request $request, Show $show)
    {
        $this->authorize('update', $show);
        $request->validate([
            'add' => 'array',
            'add.*' => 'email|distinct|exists:users,email',
            'remove' => 'array',
            'remove.*' => 'email|distinct|exists:users,email',
        ]);

        foreach (($request->input('add') ?? []) as $new_email) {
            $host = User::where('email', $new_email)->first();

            if (! ($show->hosts->contains($host) or $show->invitees->contains($host))) {
                $show->invitees()->attach($host->id);
                $host->notify(new ShowInvitation($show, $request->user()));
            }
        }

        foreach (($request->input('remove') ?? []) as $new_email) {
            $host = User::where('email', $new_email)->first();

            $show->hosts()->detach($host->id);
            $show->invitees()->detach($host->id);
        }

        return $show;
    }

    /**
     * Respond to a join invitation.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function join(Request $request, Show $show)
    {
        $request->validate(['token' => 'required|string']);
        try {
            $this->validateToken($request->input('token'), $request->user()->email, $show->id);
        } catch (DecryptException $e) {
            abort(400, 'The token is invalid.');
        }

        $show->invitees()->detach($request->user()->id);
        if (! $request->has('cancel')) {
            $show->hosts()->detach($request->user()->id);
            $show->hosts()->attach($request->user()->id, ['accepted' => true]);
            $this->generateCertificate($request->user(), $show, $show->term);
        }

        return $show;
    }

    /**
     * Validate a join token.
     *
     * @param  string  $token
     * @param  string  $email
     * @param  string  $show
     * @throws DecryptException
     * @return void
     */
    private function validateToken(string $token, string $email, string $show)
    {
        $data = decrypt($token);
        if (! is_array($data)) {
            throw new DecryptException('The given token is not an array.');
        } elseif (! array_key_exists('show', $data) or ! array_key_exists('user', $data)) {
            throw new DecryptException('The token does not have the required components.');
        } elseif ($data['user'] != $email) {
            throw new DecryptException('The token does not belong to you.');
        } elseif ($data['show'] != $show) {
            throw new DecryptException('The token does not belong to this show.');
        }
    }

    /**
     * Validate a show and submit it, or remove submission status.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return KRLX\Show|Illuminate\Http\Response
     */
    public function submit(Request $request, Show $show)
    {
        $this->authorize('update', $show);
        if ($request->input('submitted') and ! $show->submitted) {
            $ruleset = new ShowRuleset($show, $request->all());
            $rules = $ruleset->rules(true);

            Validator::make($show->toArray(), $rules)->validate();

            Mail::to($show->hosts)->queue(new ShowSubmitted($show));
        }
        $show->submitted = $request->input('submitted') ?? false;
        $show->save();

        return $show;
    }

    /**
     * Send reminder emails to incomplete shows.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function remind(Request $request)
    {
        $request->validate([
            'term_id' => 'required|string|exists:terms,id',
        ]);

        $term = Term::find($request->input('term_id'));
        if (! $term->applications_close) {
            // The term MUST have an Applications Close date set.
            // Otherwise, emails will have issues rendering.
            abort(400, 'This term has not been fully configured. Please set an application closure date in the term settings.');
        }

        $shows = $term->shows()->where('submitted', false)->with('hosts')->get();

        foreach ($shows as $show) {
            Mail::to($show->hosts)->queue(new ShowReminder($show));
        }
    }

    /**
     * Generates a Board Upgrade Certificate if the show and user are eligible.
     *
     * @param  KRLX\User  $user
     * @param  KRLX\Show  $show
     * @param  KRLX\Term  $term
     * @return void
     */
    public function generateCertificate(User $user, Show $show, Term $term)
    {
        if ($user->can('auto-request Zone S') and $show->track->boostable) {
            $boosts = $user->boosts()->with('show')->where('type', 'S')->get();
            $boosted_shows = $boosts->filter(function ($boost) use ($term) {
                return $boost->term_id == $term->id or ($boost->show and $boost->show->term_id == $term->id);
            });
            if ($boosted_shows->count() == 0) {
                $user->boosts()->create(['type' => 'S', 'show_id' => $show->id, 'term_id' => $term->id]);
            }
        }
    }
}
