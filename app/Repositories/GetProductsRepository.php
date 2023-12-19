<?php


namespace App\Repositories;


use App\Contracts\ProductContract;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;

class GetProductsRepository implements \App\Contracts\GetProductsInterface
{
    /**
     * @param ProductContract $productContract
     * @param $vendor
     * @return mixed
     */
    public function getProducts(ProductContract $productContract, Vendor $vendor): mixed
    {
        return $productContract->buildQuery(
            Product::query()
        )
            ->select(['id' , 'name' , 'collection_id' ,'image' , 'price','price_after_discount'])
            ->allowedFilters('collection_id')
            ->whereBelongsTo($vendor)
            ->jsonPaginate();
    }
}