<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', "in:" . Order::ORDER_APPROVED . "," . Order::ORDER_SHIPPED . "," . Order::ORDER_DONE]
        ]);

        $order->update([
            'status' => $request->status
        ]);

        info("Order status got updated to {$request->status}");

        return $this->apiResponse(
            data: OrderResource::make($order->fresh('vendor', 'user')),
            code: JsonResponse::HTTP_CREATED
        );
    }
}