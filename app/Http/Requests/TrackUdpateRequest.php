<?php

namespace KRLX\Http\Requests;

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
        $rules = [
            'name' => 'string|min:3|max:190|unique:tracks',
            'description' => 'string|min:20',
            'active' => 'boolean',
            'boostable' => 'boolean',
            'clonable' => 'boolean',
            'allows_images' => 'boolean',
            'can_fall_back' => 'boolean',
            'taggable' => 'boolean',
            'awards_xp' => 'boolean',
            'prefix' => 'nullable|string',
            'zone' => 'nullable|string|alpha|size:1',
            'group' => 'nullable|integer|min:0|max:100',
            'order' => 'integer|min:0|max:65500',
            'allows_direct_add' => 'boolean',
            'joinable' => 'boolean',
            'max_participants' => 'nullable|integer|min:0|max:200',
            'title_label' => 'nullable|string|max:190',
            'description_label' => 'nullable|string|max:190',
            'description_min_length' => 'nullable|integer|min:0|max:65500',
            'weekly' => 'boolean',
            'start_day' => [
                'nullable',
                'required_with_all:start_time,end_time',
                'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
            ],
            'start_time' => [
                'nullable',
                'required_with_all:start_day,end_time',
                'regex:/^(([01][0-9])|(2[0-3])):[0-9]{2}$/'
            ],
            'end_time' => [
                'nullable',
                'required_with_all:start_day,start_time',
                'regex:/^(([01][0-9])|(2[0-3])):[0-9]{2}$/'
            ]
        ];

        if($this->isMethod('PUT')) {
            foreach($rules as $field => &$checks) {
                if(is_array($checks)) {
                    array_prepend($checks, 'present');
                } else {
                    $checks = "present|$checks";
                }
            }
        }

        $specials = ['content', 'scheduling', 'etc'];
        foreach($specials as $special) {
            $rules = array_merge($rules, [
                $special => ($this->isMethod('PUT') ? 'present|' : '').'nullable|array|min:0',
                "$special.*.title" => 'required|string',
                "$special.*.db" => 'required|string',
                "$special.*.helptext" => 'present|nullable|string',
                "$special.*.type" => 'required|in:'.implode(',', array_keys(config('fields'))),
                "$special.*.rules" => 'present|array|min:0',
                "$special.*.options" => "required_if:$special.*.type,".implode(',', array_keys(collect(config('fields'))->where('has_options', true)->all())).'|array|min:0',
                "$special.*.options.*.title" => "required_with:$special.*.options|string",
                "$special.*.options.*.value" => "required_with:$special.*.options|string",
                "$special.*.options.*.default" => "required_with:$special.*.options|boolean"
            ]);
        }

        return $rules;
    }
}
