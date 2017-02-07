<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePost extends FormRequest
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
            // 'title' => 'required',
            // 'desc'  => 'required',
            // 'type'  => 'required',
            // 'outlets'   => 'required',
            // 'category'  => 'required',
            // 'subcategories' => 'required',
        ];
    }

    public function validate()
    {
        $validator = Validator::make(request()->all(), [
            'type'  => 'required',
            'outlets'    => 'required',
            'title' => 'required',
            'category'  => 'required',
            'subcategories' => 'required',
            'desc'  => 'required'
        ]);

        $validator->sometimes(['source_from', 'source_link'], 'required', function($input) {
            return $input->isExternal == 1;
        });

        $validator->sometimes(['event_date', 'event_time', 'event_location'], 'required', function($input) {
            return $input->type == 'events';
        });

        $validator->validate();         
    }
}
