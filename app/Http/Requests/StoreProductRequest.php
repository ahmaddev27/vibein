<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            // Product
            'brandId' => 'nullable|exists:brand,id',
            'label' => 'nullable|string|max:255',

            // Translation
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'nullable|string',
            'metaTagTitle' => 'nullable|string|max:255',

            // Categories
            'category_id' => 'nullable|array|min:1',
//            'category_id.*' => 'exists:categories,id',
            'sub_category_id' => 'required|array|min:1',
            'sub_category_id.*' => 'nullable',
            'images'=> 'nullable|array|min:1',
            'quantity' => 'required|numeric|min:1',
            // Pieces
            'prices' => 'required|array|min:1',
            'prices.*.weight' => 'required|numeric|min:0.01',
            'prices.*.price' => 'required|numeric|min:0.01'
        ];
    }


    public function prepareForValidation()
    {
        $this->merge([
            'companyId' => 31, // Set the companyId to a default value
            'languageCode' => 'en', // Default to 'en' if not provided
        ]);
    }


}
