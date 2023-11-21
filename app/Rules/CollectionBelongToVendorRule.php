<?php

namespace App\Rules;

use App\Models\Collection;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

class CollectionBelongToVendorRule implements ValidationRule
{
    public function __construct(private ?Model $product = null)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->product instanceof Model) {
            $vendor_id = Collection::where('id', $value)->select('vendor_id')->value('vendor_id');

            if (isset($value) && $vendor_id != request()->user('api-vendor')->id) {
                $fail('This collection doesn\'t belong the logged in user.');
            }
        }
    }
}