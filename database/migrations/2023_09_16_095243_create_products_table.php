<?php

use App\Models\Collection;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->foreignIdFor(Vendor::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Collection::class)->nullable()->nullOnDelete();
            $table->decimal('price' , 10,2);
            $table->decimal('price_after_discount' , 10,2)->nullable();
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};