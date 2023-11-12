<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductControllerContract
{
    public function storeProduct(Request $request): Product;
    public function updateProduct(Request $request, Product $product): Product;
    public function ensureVendorOwnsProduct(Request $request, Product $product);
}