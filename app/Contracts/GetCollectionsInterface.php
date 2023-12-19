<?php


namespace App\Contracts;

use App\Models\Vendor;

interface GetCollectionsInterface
{
    public function getCollections(CollectionContract $collectionContract , Vendor $vendor);
}