<?php

namespace App\Repositories;

use App\Contracts\CollectionContract;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

final class FilterForCollectionContract implements CollectionContract
{
    public function buildQuery(Builder $query): QueryBuilder
    {
        return QueryBuilder::for($query);
    }
}
