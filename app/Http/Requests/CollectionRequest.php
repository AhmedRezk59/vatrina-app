<?php

namespace App\Http\Requests;

class CollectionRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->routeIs('vendor.collections.update')
            ? (\request()->user('api-vendor')->id == $this->collection->id)
            : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required','string' , 'max:55' , 'min:3']
        ];
    }
}
