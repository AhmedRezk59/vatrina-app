<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Vendor;

interface OrderControllerRepositoryInterface
{
    public function createOrder(Vendor $vendor): Order;
    public function logNewOrderCreated(Vendor $vendor): void;
    public function logNewOrderFailedToCreate(Vendor $vendor, \Throwable $th): void;
    public function emptyUserCart(Vendor $vendor): void;
}