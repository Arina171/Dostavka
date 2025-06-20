<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); 
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity'); 
            $table->decimal('price_at_order', 10, 2); 
            $table->primary(['order_id', 'product_id']); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};