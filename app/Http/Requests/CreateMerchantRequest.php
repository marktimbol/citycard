<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantRequest extends FormRequest
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
            'area' => 'required',
            'name'	=> 'required|unique:merchants',
			'email'	=> 'required|email|unique:merchants',
            'phone'  => 'required',
            'currency'  => 'required',
            'category'  => 'required',
            'subcategories' => 'required',
			'password'	=> 'required|min:6|confirmed',
        ];
    }
}
