<?php

use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vendor::class)->nullOnDelete();
            $table->foreignIdFor(User::class)->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('status', [Order::ORDER_PENDING, Order::ORDER_APPROVED, Order::ORDER_CANCELED, Order::ORDER_SHIPPED, Order::ORDER_DONE])->default(Order::ORDER_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};