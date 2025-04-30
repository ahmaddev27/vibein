<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class MachineRequest extends FormRequest
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
            'status' => 'nullable|string|in:Active,Inactive',
            'size' => 'required|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'category_id' => 'required|exists:category,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

    public function prepareForValidation()
    {
        $this->merge([
            'status' => 'Active',
        ]);
    }

}
