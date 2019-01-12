<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\Jobs\PublishShow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use KRLX\Jobs\FinalPublishShow;
use KRLX\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    /**
     * Save changes to the working show times.
     *
     * @param  KRLX\Show  $show
     * @return KRLX\Show
     */
    public function update(Request $request, Show $show)
    {
        $request->validate([
            'date' => ['sometimes', 'nullable', 'date'],
            'day' => ['sometimes', 'nullable', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start' => ['sometimes', 'nullable', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
            'end' => ['sometimes', 'nullable', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
        ]);

        foreach ($request->only('date', 'day', 'start', 'end') as $key => $value) {
            $show->{$key} = $value;
        }
        // This line seems silly - it adds updated_at to the list of "dirty" attributes,
        // and therefore insists on keeping updated_at the same.
        $show->updated_at = $show->updated_at;
        $show->save();

        return $show;
    }

    /**
     * Synchronize multiple shows to the calendar at once.
     *
     * @param Request $request
     * @return void
     */
    public function sync(Request $request)
    {
        $request->validate([
            'shows' => 'array',
            'shows.*.id' => ['required', 'string', 'exists:shows,id'],
            'shows.*.date' => ['sometimes', 'nullable', 'date'],
            'shows.*.day' => ['sometimes', 'nullable', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'shows.*.start' => ['sometimes', 'nullable', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
            'shows.*.end' => ['sometimes', 'nullable', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
        ]);

        $shows = [];
        foreach($request->input('shows') as $data) {
            // Show is guaranteed to exist because otherwise the inbound request would fail validation.
            $show = Show::find($data['id']);
            if (array_key_exists('date', $data)) {
                $show->date = $data['date'];
            } else {
                $show->day = $data['day'];
                $show->start = $data['start'];
                $show->end = $data['end'];
            }

            // Multi-sync should preserve priority.
            $show->updated_at = $show->updated_at;
            $show->save();
            $shows[] = $show;
        }

        return collect($shows)->pluck('id');
    }

    /**
     * Get the status of an ongoing publish.
     *
     * @return Illuminate\Http\Response
     */
    public function status()
    {
        $data = array_wrap(json_decode(file_get_contents(storage_path('app/publish')), true));
        if (! isset($data['show']) or ! isset($data['position']) or $data['position'] == 0) {
            return response(null, 204);
        } else {
            return response($data);
        }
    }

    /**
     * Dispatch schedule publication jobs to the queue. Returns a response with
     * HTTP 202 and a pointer to the monitoring route.
     *
     * @return Illuminate\Http\Response
     */
    public function publish(Request $request)
    {
        $request->validate([
            'publish' => 'sometimes|array',
            'publish.*' => ['string', Rule::exists('shows', 'id')->where(function ($query) {
                $query->where('submitted', true);
            })],
            'final' => 'sometimes|nullable|exists:terms,id',
        ]);
        $count = 0;

        if ($request->has('final') and $request->input('final') != null) {
            $shows = Term::find($request->input('final'))->shows()->where('submitted', true)->whereNotNull('start')->whereNotNull('end')->whereNotNull('day')->get();

            file_put_contents(storage_path('app/publish'), json_encode(['position' => 0, 'max' => $shows->count(), 'show' => '']));
            $count = $shows->count();

            foreach ($shows as $show) {
                FinalPublishShow::dispatch($show);
            }
        } else {
            file_put_contents(storage_path('app/publish'), json_encode(['position' => 0, 'max' => count($request->input('publish')), 'show' => '']));
            $count = count($request->input('publish'));

            foreach ($request->input('publish') as $show_id) {
                $show = Show::find($show_id);
                PublishShow::dispatch($show, true);
            }
        }

        return response([
            'job_status' => 'queued',
            'tasks' => $count,
            'monitor' => '/api/v1/schedule/publish',
        ], 202);
    }
}
