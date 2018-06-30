<?php

namespace KRLX\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TermUpdateRequest extends FormRequest
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
        return [
            'on_air' => ($this->isMethod('PATCH') ? 'required_with:off_air' : 'required').'|date',
            'off_air' => ($this->isMethod('PATCH') ? 'required_with:on_air' : 'required').'|date|after::on_air',
            'accepting_applications' => ($this->isMethod('PATCH') ? 'sometimes' : 'required').'|boolean'
        ];
    }
}
