<?php

namespace KRLX\Http\Resources;

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
            'djs' => $this->hosts->pluck('full_name'),
        ];

        $weekly = [
            'start' => $this->start,
            'end' => $this->end,
            'day' => $this->day,
        ];

        $date = [
            'date' => $this->date,
            'start' => $this->date ? $this->track->start_time : null,
            'end' => $this->date ? $this->track->end_time : null,
        ];

        return array_merge($base, ($this->track->weekly ? $weekly : $date));
    }
}
