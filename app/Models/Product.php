<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!isset($model->vendor_id)) {
                $model->vendor_id = request()->user('api-vendor')->id;
            }
        });
    }


    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function endPrice(): float
    {
        if ($this->price_after_discount == 0) {
            return (float) $this->price;
        }
        return (float)$this->price_after_discount;
    }
}