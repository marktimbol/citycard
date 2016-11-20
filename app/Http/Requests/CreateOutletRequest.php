<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOutletRequest extends FormRequest
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
            'name'	=> 'required',
			'email'	=> 'required|email',
			'phone'	=> 'required',
			'password'	=> 'required|min:6|confirmed',
			'password_confirmation'	=> 'required'
        ];
    }
}
