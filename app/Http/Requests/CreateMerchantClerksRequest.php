<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMerchantClerksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guard('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'    => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:clerks',
            'password'  => 'required|min:6|confirmed',
            'password_confirmation' => 'required'
        ];
    }
}
