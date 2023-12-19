<?php


namespace App\Contracts;

use App\Models\Vendor;

interface GetProductsInterface
{
    public function getProducts(ProductContract $productContract,Vendor $vendorUserName);
}