<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "username" => $this->username,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "avatar" => 'storage/vendors/avatars/' . $this->id  .$this->avatar,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
