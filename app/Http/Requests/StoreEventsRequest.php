<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventsRequest extends FormRequest
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
            'valid_from' => 'required|date|after:today',
            'valid_to' => 'required|date|after:valid_from',
            'title' => 'required|unique:news|max:255',
            'content' => 'required',
            'gps_lat' => 'required|numeric',
            'gps_lng' => 'required|numeric',
        ];
    }
}
