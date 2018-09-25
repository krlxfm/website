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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'djs' => $this->hosts->pluck('full_name'),
            'start' => $this->start,
            'end' => $this->end,
            'day' => $this->day,
        ];
    }
}
