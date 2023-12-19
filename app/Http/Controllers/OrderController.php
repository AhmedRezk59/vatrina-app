<?php

namespace App\Http\Controllers;

use App\Contracts\OrderControllerRepositoryInterface;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Vendor;
use App\Services\SendWhatsappMessage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = auth()->user('api')->orders()->with('products')->jsonPaginate(10);

        return $this->apiResponse(
            data: $orders
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderControllerRepositoryInterface $orderControllerRepository, Vendor $vendor)
    {
        Gate::forUser($vendor)->authorize('ensure-vendor-is-not-banned');
        
        try{
            DB::transaction(function () use ($orderControllerRepository, $vendor) {
                $orderControllerRepository->createOrder($vendor);
                $orderControllerRepository->emptyUserCart($vendor);
            }, 5);
            $orderControllerRepository->logNewOrderCreated($vendor);
            (new SendWhatsappMessage($vendor ,"new order got created by " . auth()->user()->name ))->send();
            (new SendWhatsappMessage(auth()->user() ,"You have created order successfully." ))->send();

        }catch(\Throwable){
            $orderControllerRepository->logNewOrderFailedToCreate($vendor, $th);
        }

        return $this->apiResponse(
            msg: "Order got placed successfully."
        );
    }

    /**
     * Display the specified resource.
     */
    public function show($order)
    {
        $order = Order::where('user_id', auth('api')->user()->id)->where('id', $order)->with(['user', 'vendor'])->firstOrFail();

        return $this->apiResponse(
            data: OrderResource::make($order)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        Gate::forUser($order->vendor)->authorize('ensure-vendor-is-not-banned');
        
        $order->update($request->validated());

        return $this->apiResponse(
            data: OrderResource::make($order->fresh(['user', 'vendor'])),
            code: 201
        );
    }
}