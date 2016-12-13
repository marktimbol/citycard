<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source'    => 'required',
            'source_from'    => 'required',
            'source_link'    => 'required',
            'type'  => 'required',
            'outlet_ids'    => 'required',
            'title' => 'required|unique:posts',
            'category'  => 'required',
            'subcategories' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'source.required'   => 'External source is required',
            'source_from.required'   => 'From which source you get this?',
            'source_link.required'   => 'Please enter the external link of the post',
            'type.required'   => 'Please specify the Post Type',
            'outlet_ids.required'   => 'Please select Outlets'
        ];
    }
}
