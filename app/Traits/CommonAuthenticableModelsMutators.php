<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

trait CommonAuthenticableModelsMutators
{
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function password(): Attribute
    {
        return new Attribute(
            set: fn ($value) => Hash::make($value)
        );
    }

    public function name(): Attribute
    {
        return new Attribute(
            get: fn () => $this->first_name . " " . $this->last_name
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}