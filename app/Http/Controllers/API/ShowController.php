<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\User;
use Illuminate\Http\Request;
use KRLX\Rulesets\ShowRuleset;
use Illuminate\Validation\Rule;
use KRLX\Http\Controllers\Controller;
use KRLX\Http\Requests\ShowUpdateRequest;

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
     * Validate a show and submit it, or remove submission status.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  KRLX\Show  $show
     * @return Illuminate\Http\Response
     */
    public function submit(Request $request, Show $show)
    {
        $ruleset = new ShowRuleset($show, $request->all());
    }
}
