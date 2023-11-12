<?php

namespace App\Contracts;

use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder;

interface ProductContract
{
    public function buildQuery(Builder $query): QueryBuilder;
}
