<?php


namespace App\Repositories;


use App\Contracts\ProductContract;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class GetProductsRepository implements \App\Contracts\GetProductsInterface
{
    /**
     * @param ProductContract $productContract
     * @param $vendorUserName
     * @return mixed
     */
    public function getProducts(ProductContract $productContract, $vendorUserName): mixed
    {
        return $productContract->buildQuery(
            Product::query()
        )
            ->select(['id' , 'name' , 'collection_id' ,'image' , 'price','price_after_discount'])
            ->allowedFilters('collection_id')
            ->whereRelation('vendor' , 'username' ,'=', $vendorUserName)
            ->jsonPaginate();
    }
}
