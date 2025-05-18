<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreBrandRequest extends FormRequest
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
            // Brand fields
//            'companyId' => 'required|integer|exists:companies,id',
//            'showStatus' => 'required|integer|in:0,1',
            'sortOrder' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Translation fields
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'metaTagTitle' => 'nullable|string|max:255',
            'metaTagDescription' => 'nullable|string',
            'metaTagKeywords' => 'nullable|string',
//            'languageCode' => 'required|string|size:2',
        ];
    }

    public function messages()
    {
        return [
//            'companyId.required' => 'Company ID is required.',
            'name.required' => 'Brand name is required.',
//            'languageCode.required' => 'Language code is required.',
        ];
    }

    public function attributes()
    {
        return [
            'companyId' => 'company',
            'showStatus' => 'status',
            'metaTagTitle' => 'meta title',
            'metaTagDescription' => 'meta description',
            'metaTagKeywords' => 'meta keywords',
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

    public function prepareForValidation()
    {
        $this->merge([
            'showStatus' => $this->showStatus ?? true, // Default to 1 if not provided
            'languageCode' => 'en', // Default to 'en' if not provided
        ]);
    }

}
