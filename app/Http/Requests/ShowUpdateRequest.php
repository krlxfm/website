<?php

namespace KRLX\Http\Requests;

use KRLX\Track;
use KRLX\Rules\Profanity;
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
        $rules = array_merge(
            $this->baseRules(),
            $this->trackDependentRules($track),
            $this->customFieldRules($track)
        );

        if ($this->isMethod('PUT')) {
            foreach ($rules as $field => &$ruleset) {
                $ruleset = array_prepend($ruleset, (head($ruleset) == 'nullable' or in_array('min:0', $ruleset)) ? 'present' : 'required');
            }
        }

        return $rules;
    }

    /**
     * Returns the "base" rules, applicable to all tracks.
     *
     * @return array
     */
    protected function baseRules()
    {
        $baseRules = [
            'submitted' => ['boolean'],
            'title' => ['string', 'min:3', 'max:200', new Profanity],
            'content' => ['array'],
            'scheduling' => ['array'],
            'conflicts' => ['array', 'min:0'],
            'preferences' => ['array'],
            'etc' => ['array'],
            'special_times' => ['array', 'size:'.count(config('defaults.special_times'))],
            'classes' => ['array'],
            'classes.*' => ['string'],
            'tags.*' => ['string'],
            'preferred_length' => ['integer', 'min:0', 'max:240'],
            'notes' => ['nullable', 'string', 'max:65000'],
            'date' => ['nullable', 'date'],
            'day' => ['nullable', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            'start' => ['nullable', 'string', 'regex:([01][0-9]|2[0-3]):[0-5][0-9]'],
            'end' => ['nullable', 'string', 'regex:([01][0-9]|2[0-3]):[0-5][0-9]'],
        ];

        foreach (config('defaults.special_times') as $time => $details) {
            $baseRules["special_times.$time"] = ['string', 'in:y,m,n'];
        }

        return $baseRules;
    }

    /**
     * Returns the basic which change based on the track settings - note that
     * these are NOT the custom fields.
     *
     * @param  KRLX\Track  $track
     * @return array
     */
    protected function trackDependentRules(Track $track)
    {
        $trackDepRules = [
            'description' => ['min:'.$track->description_min_length, 'max:65000'],
            'conflicts.*' => ($track->weekly ? ['array'] : ['date', 'distinct', Rule::notIn(array_wrap($this->input('preferences') ?? $this->show->preferences))]),
            'preferences.*' => ($track->weekly ? ['array'] : ['date', 'distinct', Rule::notIn(array_wrap($this->input('conflicts') ?? $this->show->conflicts))]),
            'tags' => ['array', ($track->taggable ? 'min:0' : 'max:0')],
        ];

        if ($track->weekly) {
            $trackDepRules['conflicts.*.days'] = ['array', 'min:1', 'max:7', 'distinct'];
            $trackDepRules['conflicts.*.days.*'] = ['string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'];
            $trackDepRules['conflicts.*.start'] = ['string', 'regex:/([01][0-9]|2[0-3]):[03]0/'];
            $trackDepRules['conflicts.*.end'] = ['string', 'regex:/([01][0-9]|2[0-3]):[03]0/'];
            $trackDepRules['preferences.*.days'] = ['array', 'min:1', 'max:7', 'distinct'];
            $trackDepRules['preferences.*.days.*'] = ['string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'];
            $trackDepRules['preferences.*.start'] = ['string', 'regex:/([01][0-9]|2[0-3]):[03]0/'];
            $trackDepRules['preferences.*.end'] = ['string', 'regex:/([01][0-9]|2[0-3]):[03]0/'];
            $trackDepRules['preferences.*.strength'] = ['integer', 'min:0', 'max:200'];
        }

        return $trackDepRules;
    }

    /**
     * Returns the rules for custom fields.
     *
     * @param  KRLX\Track  $track
     * @return array
     */
    protected function customFieldRules(Track $track)
    {
        $custom = ['content', 'scheduling', 'etc'];
        $rules = [];
        foreach ($custom as $category) {
            foreach ($track->{$category} as $field) {
                $rules[$category.'.'.$field['db']] = $this->processCustomFieldRules($field['rules']);
            }
        }

        return $rules;
    }

    /**
     * Process the validation rules for a given custom field.
     *
     * @param  array  $rules
     * @return array
     */
    protected function processCustomFieldRules(array $rules)
    {
        $filtered_rules = array_where($rules, function ($value, $key) {
            return $value != 'required';
        });

        return $filtered_rules;
    }
}
