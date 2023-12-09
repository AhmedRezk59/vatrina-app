<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addProductToTheCart(CartRequest $request, Vendor $vendor, Product $product)
    {
        auth('api')->user()->cart()->create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'quantity' =>  $request->quantity
        ]);

        return $this->apiResponse(
            msg: "Product got added to the cart"
        );
    }

    public function updateProductQuantity(CartRequest $request, Vendor $vendor, Product $product)
    {
        auth('api')->user()->cart()
            ->whereBelongsTo($vendor)
            ->where('product_id', $product->id)
            ->update($request->validated());

        return $this->apiResponse(
            msg: "Product quantity got updated successfully."
        );
    }

    public function removeProductfromTheCart(Vendor $vendor, Product $product)
    {
        auth('api')->user()->cart()
            ->whereBelongsTo($vendor)
            ->where('product_id', $product->id)
            ->delete();

        return $this->apiResponse(
            msg: "Product got removed to the cart"
        );
    }
}