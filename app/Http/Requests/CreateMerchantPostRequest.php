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
            'subcategories' => 'required',
            'desc' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'desc.required'   => 'The Post description is required',
        ];
    }
}
