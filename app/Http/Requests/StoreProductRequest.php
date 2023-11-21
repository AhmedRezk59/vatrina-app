<?php

namespace App\Http\Requests;

use App\Rules\CollectionBelongToVendorRule;

class StoreProductRequest extends ApiRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:191'],
            'description' => ['required', 'string', 'min:55', 'max:500'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'dimensions:min_width=150,min_height=150,max_width=500,max_height=500'],
            'amount' => ['required', 'integer'],
            'price' => ['required', 'numeric', 'digits_between:1,10'],
            'price_after_discount' => ['required', 'numeric', 'digits_between:1,10'],
            'collection_id' => ['required', 'integer', 'exists:collections,id', new CollectionBelongToVendorRule()]
        ];
    }
}
