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
            'type.required'   => 'Please specify the Post Type',
            'outlet_ids.required'   => 'Please select Outlets'
        ];
    }
}
