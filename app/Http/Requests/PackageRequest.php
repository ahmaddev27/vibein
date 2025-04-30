<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class PackageRequest extends FormRequest
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
            'price' => 'required|numeric|min:0',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:product,id',
            'products.*.position' => 'required|integer|min:1',
            'products.*.is_selected' => 'required|boolean',
            'alternatives' => 'array',
            'alternatives.*.product_id' => 'required|exists:product,id',
            'alternatives.*.position' => 'required|integer|min:1',
            'alternatives.*.is_selected' => 'required|boolean',
            'alternatives.*.add_on' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
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
