<?php

namespace KRLX\Http\Requests;

use KRLX\Track;
use KRLX\Rules\Profanity;
use KRLX\Rulesets\ShowRuleset;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ShowUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $track = Track::find($this->input('track_id')) ?? $this->show->track;
        $ruleset = new ShowRuleset($this->show, $this->all(), $track);

        return $ruleset->rules($this->isMethod('PUT'));
    }
}
