<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\Jobs\PublishShow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'date' => ['sometimes', 'date'],
            'day' => ['sometimes', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start' => ['sometimes', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
            'end' => ['sometimes', 'string', 'regex:/([01][0-9]|2[0-3]):[0-5][0-9]/'],
        ]);

        foreach ($request->only('date', 'day', 'start', 'end') as $key => $value) {
            $show->{$key} = $value;
        }
        $show->save();

        return $show;
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
            'publish' => 'required|array|min:1',
            'publish.*' => ['string', Rule::exists('shows', 'id')->where(function ($query) {
                $query->where('submitted', true);
            })]
        ]);

        foreach($request->input('publish') as $show_id) {
            $show = Show::find($show_id);
            // PublishShow::dispatch($show);
        }

        return response([
            'job_status' => 'queued',
            'tasks' => count($request->input('publish')),
            'monitor' => '/api/v1/schedule/publish'
        ], 202);
    }
}
