<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rules;

class UserRegistrationRequest extends ApiRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:55', 'unique:' . User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20', 'min:8'],
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg', 'dimensions:min_width=150,min_height=150,max_width=500,max_height=500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}