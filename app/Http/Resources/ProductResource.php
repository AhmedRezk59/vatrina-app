<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => route('user.interface.product.image', $this->id),
            'amount' => $this->amount,
            'price' => $this->price,
            'price_after_discount' => $this->price_after_discount,
            'collection' => CollectionResource::make($this->collection),
            'vendor' => VendorResource::make($this->vendor)
        ];
    }
}