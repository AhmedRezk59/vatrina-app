<?php

namespace App\Repositories;

use App\Contracts\ProductControllerContract;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductControllerRepository implements ProductControllerContract
{
    public function storeProduct(Request $request): Product
    {
        $product = Product::create([
            ...$request->validated(),
            'image' => $request->file('image')->hashName()
        ]);

        $request->file('image')->store('/vendors/products/' . $request->user('api-vendor')->id, 'public');
        return $product;
    }

    public function updateProduct(Request $request, $product): Product
    {
        $data = [
            ...$request->validated()
        ];

        if (isset($request->image)) {
            $data['image'] = $request->file('image')->hashName();
            Storage::disk('public')->delete('vendors/products/' . $product->vendor_id . '/' . $product->image);
            $request->file('image')->store('/vendors/products/' . $product->vendor_id, 'public');
        }

        $product->update($data);
        $product->fresh(['collection', 'vendor']);
        return $product;
    }

    public function ensureVendorOwnsProduct($request, $product)
    {
        if ($product->vendor_id != $request->user('api-vendor')->id) {
            throw new HttpResponseException(
                response()->json(
                    [
                        'msg' => 'This vendor is not the owner of this product'
                    ],
                    401
                ),
            );
        }
    }
}