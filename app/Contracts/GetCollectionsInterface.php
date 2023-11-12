<?php


namespace App\Contracts;


interface GetCollectionsInterface
{
    public function getCollections(CollectionContract $collectionContract , $vendorUserName);
}
