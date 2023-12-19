<?php


namespace App\Repositories;


use App\Contracts\CollectionContract;
use App\Models\Collection;
use App\Models\Vendor;

class GetCollectionsRepository implements \App\Contracts\GetCollectionsInterface
{
    /**
     * @param CollectionContract $collectionContract
     * @param $vendor
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getCollections(CollectionContract $collectionContract, Vendor $vendor): \Illuminate\Database\Eloquent\Collection|array
    {
        return $collections = $collectionContract->buildQuery(
            Collection::query()
        )
            ->whereBelongsTo($vendor)
            ->select(['id' , 'name'])
            ->get();
    }
}