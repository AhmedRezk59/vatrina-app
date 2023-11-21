<?php

namespace App\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

interface CollectionContract
{
    public function buildQuery(Builder $query) : QueryBuilder;
}
