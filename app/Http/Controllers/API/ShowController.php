<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use KRLX\Http\Controllers\Controller;
use KRLX\Notifications\ShowInvitation;
use KRLX\Http\Requests\ShowUpdateRequest;
use KRLX\Notifications\NewUserShowInvitation;
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
        $request->validate([
            'title' => 'sometimes|string|min:3',
            'track_id' => 'required|integer|exists:tracks,id',
            'term_id' => [
                'required',
                'string',
                Rule::exists('terms', 'id')->where(function ($query) {
                    $query->where('accepting_applications', true);
                }),
            ],
            'source' => 'sometimes|string|min:3|regex:[A-Za-z][A-Za-z0-9-_\./:]+',
        ]);

        $show = $request->user()->shows()->create($request->all(), ['accepted' => true]);

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
        return $show->with(['hosts', 'invitees'])->first();
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
        $request->validate([
            'invite' => 'array',
            'invite.*' => 'email|distinct',
        ]);

        foreach ($request->input('invite') as $new_email) {
            $host = User::where('email', $new_email)->first();

            if(! $host) {
                $host = User::create(['email' => $new_email, 'name' => 'Temporary User']);
                $host->notify(new NewUserShowInvitation($show, $request->user()));
                $host->delete(); // Just to be safe.
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
        $request->validate(['token' => 'required|string']); // (1)
        try {
            $data = decrypt($request->input('token')); // (2)
            if (! is_array($data)) { // (3)
                throw new DecryptException('The given token is not an array.');
            } elseif (! array_key_exists('show', $data) or ! array_key_exists('user', $data)) { // (4)
                throw new DecryptException('The token does not have the required components.');
            } elseif ($data['user'] != $request->user()->email) { // (5)
                throw new DecryptException('The token does not belong to you.');
            } elseif ($data['show'] != $show->id) { // (6)
                throw new DecryptException('The token does not belong to this show.');
            }
        } catch (DecryptException $e) {
            abort(400, 'The token is invalid.');
        }

        // We now know that: (1) the token is present, (2) it is encrypted,
        // (3) the decrypted form is an array, (4) it has the required
        // components, (5) it belongs to the user, and (6) it belongs to the
        // show being joined. This request is therefore authorized and we can
        // now make the connection.
        $show->invitees()->detach($request->user()->id);
        $show->hosts()->detach($request->user()->id);
        $show->hosts()->attach($request->user()->id, ['accepted' => true]);

        return $show;
    }
}
