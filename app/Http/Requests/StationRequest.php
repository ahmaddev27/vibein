<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StationRequest extends FormRequest
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

            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:255',
            'features' => 'nullable|array',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:category,id',
            'sort_order' => 'nullable|integer',
            'is_recommended' => 'nullable|boolean',
            'images.*.image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the max size as needed
        ];
    }


    public function attributes()
    {
        return [
            'meta_title' => 'meta title',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'Validation errors',
                'data' => null,
                'errors' => $validator->errors(),

            ], 422)
        );
    }


}
