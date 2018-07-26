<?php

namespace KRLX\Http\Requests;

use KRLX\Rules\ValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class TrackUdpateRequest extends FormRequest
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
        $rules = config('tracks.rules');

        if ($this->isMethod('PUT')) {
            foreach ($rules as $field => &$checks) {
                array_prepend($checks, 'present');
            }
        }

        $specials = ['content', 'scheduling', 'etc'];
        foreach ($specials as $special) {
            $rules = array_merge($rules, $this->special($special));
        }

        return $rules;
    }

    /**
     * Rules for the "special" fields (those that allow custom questions).
     *
     * @param  string  $type
     * @return array
     */
    protected function special(string $type)
    {
        return [
            $type => ($this->isMethod('PUT') ? 'present|' : '').'nullable|array|min:0',
            "$type.*.title" => 'required|string',
            "$type.*.db" => 'required|string|regex:[a-z-_]+',
            "$type.*.helptext" => 'present|nullable|string',
            "$type.*.type" => 'required|in:'.implode(',', array_keys(config('fields'))),
            "$type.*.rules" => 'present|array|min:0',
            "$type.*.rules.*" => [new ValidatorRule],
            "$type.*.options" => "required_if:$type.*.type,".implode(',', array_keys(collect(config('fields'))->where('has_options', true)->all())).'|array|min:0',
            "$type.*.options.*.title" => "required_with:$type.*.options|string",
            "$type.*.options.*.value" => "required_with:$type.*.options|string",
            "$type.*.options.*.default" => "required_with:$type.*.options|boolean",
        ];
    }
}
