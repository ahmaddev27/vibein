<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCycleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'week_days' => 'required|array|min:1',
            'week_days*' => 'required|string|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'delivers_times'=> 'required|array|min:1',

        ];
    }
}
