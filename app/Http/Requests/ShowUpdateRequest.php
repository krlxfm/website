<?php

namespace KRLX\Http\Requests;

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
        $track = $this->show->track;
        return [
            'title' => ['string', 'min:3', 'max:200'],
            'description' => ['string']
        ];
    }
}
