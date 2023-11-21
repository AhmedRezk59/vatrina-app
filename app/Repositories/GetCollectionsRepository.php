<?php


namespace App\Repositories;


use App\Contracts\CollectionContract;
use App\Models\Collection;

class GetCollectionsRepository implements \App\Contracts\GetCollectionsInterface
{
    /**
     * @param CollectionContract $collectionContract
     * @param $vendorUserName
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getCollections(CollectionContract $collectionContract, $vendorUserName): \Illuminate\Database\Eloquent\Collection|array
    {
        return $collections = $collectionContract->buildQuery(
            Collection::query()
        )
            ->whereRelation('vendor' , 'username' ,'=' ,$vendorUserName)
            ->select(['id' , 'name'])
            ->get();
    }
}
