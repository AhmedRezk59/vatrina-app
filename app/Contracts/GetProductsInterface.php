<?php


namespace App\Contracts;


interface GetProductsInterface
{
    public function getProducts(ProductContract $productContract,$vendorUserName);
}
