<?php

namespace App\Repositories;

use App\Contracts\AdminContract;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class FilterForAdminContract implements AdminContract
{
    public function buildQuery(Builder $query): QueryBuilder
    {
        return QueryBuilder::for($query)
            ->allowedFilters(['id','first_name', 'last_name', 'email'])
            ->allowedSorts(['id', 'created_at']);
    }
}