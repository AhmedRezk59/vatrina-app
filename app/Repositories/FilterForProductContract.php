<?php

namespace App\Repositories;

use App\Contracts\ProductContract;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

final class FilterForProductContract implements ProductContract
{
    public function buildQuery(Builder $query): QueryBuilder
    {
        return QueryBuilder::for($query)
            ->allowedSorts(['id', 'created_at', 'name']);
    }
}
