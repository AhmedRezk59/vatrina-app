<?php

namespace App\Http\Controllers;

use App\Contracts\CollectionContract;
use App\Contracts\GetCollectionsInterface;
use App\Contracts\GetProductsInterface;
use App\Contracts\ProductContract;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UserVendorController extends Controller
{
    /**
     * Get Products that belong to specific vendor by its user name
     *
     * @param ProductContract $productContract
     * @param GetProductsInterface $getProducts
     * @param string $vendorUserName
     * @param null $collection_id
     * @return JsonResponse
     */
    public function getProductsVendorInterfaceForUser(ProductContract $productContract ,GetProductsInterface $getProducts,Vendor $vendor):JsonResponse
    {
        $products = $getProducts->getProducts($productContract,$vendor);

        return $this->apiResponse(
            data: $products,
            msg: "You have successfully retrieved the products for the vendor {$vendor->username}."
        );
    }

    /**
     * Get Collections that belong to specific vendor by its user name
     *
     * @param CollectionContract $collectionContract
     * @param GetCollectionsInterface $getCollections
     * @param $vendorUserName
     * @return JsonResponse
     */
    public function getCollectionsVendorInterfaceForUser(CollectionContract $collectionContract , GetCollectionsInterface $getCollections,Vendor $vendor):JsonResponse
    {
        $collections = $getCollections->getCollections($collectionContract,$vendor);

        return $this->apiResponse(
            data:$collections,
            msg: "You have successfully retrieved the collections for the vendor {$vendor->username}."
        );
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getProductForUser(Vendor $vendor , Product $product)
    {
        Gate::forUser($vendor)->authorize('vendor-owns-product',$product);

        return $this->apiResponse(
            data : ProductResource::make($product),
            msg: "Here is the product Information for {$vendor->username}"
        );
    }
}