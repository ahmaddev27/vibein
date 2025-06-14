<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
//            'price' => 'required|numeric|min:0',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cycles' => 'required|array',
            'cycles.*.id' => 'required|exists:cycles,id',
            'cycles.*.price' => 'required|numeric|min:0',
            'tags' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product,id',

            'products.*.alternatives' => 'nullable|array',
            'products.*.alternatives.*.product_id' => 'required_with:products.*.alternatives|exists:product,id',

            'products.*.alternatives.*.add_on' => 'nullable:products.*.alternatives|numeric|min:0',
        ];
    }

    /**
     * Customize the validation messages.
     *
     * @return array<string, string>
     */

}
