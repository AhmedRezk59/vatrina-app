<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\ProductContract;
use App\Contracts\ProductControllerContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function __construct(private ProductControllerContract $productRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Vendor $vendor, ProductContract $productContract)
    {
        $products  = $productContract->buildQuery(
            Product::query()
        )
            ->whereBelongsTo($vendor)
            ->with(['collection', 'vendor'])
            ->jsonPaginate(10);

        return $this->apiResponse(
            data: $products
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepository->storeProduct($request);

        return $this->apiResponse(
            data: ProductResource::make($product->load(['collection', 'vendor'])),
            code: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->apiResponse(
            data: ProductResource::make($product->load(['collection', 'vendor']))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productRepository->ensureVendorOwnsProduct($request, $product);

        $product = $this->productRepository->updateProduct($request,$product);

        return $this->apiResponse(
            data: ProductResource::make($product),
            code: 201
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, Request $request)
    {
        $this->productRepository->ensureVendorOwnsProduct($request, $product);

        $product->forceDelete();

        Storage::disk('public')->delete('/vendors/products/' . $request->user('api-vendor')->id . '/' . $product->image);

        return $this->apiResponse(
            msg: 'This product got deleted successfully'
        );
    }
}
