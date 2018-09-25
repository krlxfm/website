<?php

namespace KRLX\Http\Controllers\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use Carbon\Carbon;
use KRLX\Http\Controllers\Controller;
use KRLX\Http\Resources\Show as ShowResource;

class FeedController extends Controller
{
    /**
     * Returns the show that is on air right now.
     *
     * @return Show
     */
    public function now()
    {
        $term = Term::orderByDesc('on_air')->first();
        $weekly_tracks = Track::where('weekly', true)->get()->pluck('id');
        $shows = $term->shows()->with('hosts')->whereIn('track_id', $weekly_tracks)->get();

        $now = Carbon::now();
        $now->minute = floor($now->minute / 30) * 30;
        $now->second = 0;
        do {
            $show = $shows->where('day', $now->format('l'))->where('start', $now->format('H:i'))->first();
            $now->subMinutes(30);
        } while ($show == null);

        return $show;
    }

    /**
     * Computes the Digital Signage Feeds.
     *
     * @return Array<KRLX\Show>
     */
    public function signage()
    {
        $now = $this->now();
        $next = $now->next;

        return [
            'now' => new ShowResource($now),
            'next' => new ShowResource($next),
        ];
    }
}
