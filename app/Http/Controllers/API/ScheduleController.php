<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\Jobs\PublishShow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use KRLX\Jobs\FinalPublishShow;
use Illuminate\Support\Facades\Log;
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
        $show->save();

        return $show;
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
