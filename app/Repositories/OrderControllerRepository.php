<?php

namespace App\Repositories;

use App\Contracts\OrderControllerRepositoryInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Vendor;
use App\Services\CalculateProductsFinalPriceService;
use Illuminate\Support\Facades\Log;

class OrderControllerRepository implements OrderControllerRepositoryInterface
{

    public function __construct(private CalculateProductsFinalPriceService $calculator)
    {
    }

    public function createOrder(Vendor $vendor): Order
    {
        $order = Order::Create([
            'vendor_id' => $vendor->id,
            'user_id' => auth('api')->user()->id,
            'amount' => $this->calculator->calculate($vendor),
            'status' => Order::ORDER_PENDING
        ]);

        return $order;
    }

    public function logNewOrderCreated(Vendor $vendor): void
    {
        info("New Order got placed successfully from user " . auth()->user()->id . " for vendor " . $vendor->username);
    }

    public function logNewOrderFailedToCreate(Vendor $vendor, \Throwable $th): void
    {
        Log::error('Failed Creating order because of ' . $th->getMessage());
    }

    public function emptyUserCart(Vendor $vendor): void
    {
        Cart::whereBelongsTo($vendor)->whereBelongsTo(auth()->user())->delete();
    }
}