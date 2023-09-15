<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateVendorProfileRequest extends ApiRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:55', Rule::unique('vendors')->ignore($this->user('api-vendor')->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('vendors')->ignore($this->user('api-vendor')->id)],
            'phone_number' => ['required', 'string', 'max:20', 'min:8'],
        ];
    }
}
