<?php

namespace KRLX\Http\Resources;

use KRLX\Term;
use KRLX\User;
use Illuminate\Http\Resources\Json\JsonResource;

class Show extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $base = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'track' => $this->track->name,
            'term' => $this->term_id,
        ];

        $weekly = [
            'djs' => $this->hosts->pluck('full_name'),
            'start' => $this->start,
            'end' => $this->end,
            'day' => $this->day,
        ];

        $track_managers = Term::orderByDesc('on_air')->first()->track_managers;
        $extra_hosts = $this->track->weekly ? [] : $track_managers[$this->track->id];

        $date = [
            'djs' => User::whereIn('id', $extra_hosts)->get()->pluck('full_name'),
            'date' => $this->date,
            'start' => $this->date ? $this->track->start_time : null,
            'end' => $this->date ? $this->track->end_time : null,
        ];

        return array_merge($base, ($this->track->weekly ? $weekly : $date));
    }
}
