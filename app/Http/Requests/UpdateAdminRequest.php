<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateAdminRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->admin->is(auth('api-admin')->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:55', Rule::unique('admins')->ignore($this->user('api-admin')->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($this->user('api-admin')->id)],
            'phone_number' => ['required', 'string', 'max:20', 'min:8'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:png,jpg,jpeg', 'dimensions:min_width=150,min_height=150,max_width=500,max_height=500'],
            'password' => ['sometimes', 'nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
