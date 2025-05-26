<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            // Category fields
//            'parentCategoryId' => [
//                'nullable',
//                'integer',
//                Rule::exists('category', 'id')->where('companyId', $this->companyId)
//            ],
            'sortOrder' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'showStatus' => 'required|boolean',

            // Category Translation fields
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'metaKeyword' => 'nullable|string',
            'metaTag' => 'nullable|string',
            'languageCode' => 'required|string|size:2', // e.g. 'en', 'ar'

            // System fields
//            'companyId' => 'required|integer|exists:companies,id'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'languageCode' => 'en', // Default to 'en' if not provided
        ]);
    }

    /**
     * Ensure the input is in proper array format for translations
     */


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
