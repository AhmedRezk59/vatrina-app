<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class ApiRequest extends FormRequest
{
    abstract public function authorize(): bool;

    abstract public function rules(): array;

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}
