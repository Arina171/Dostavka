<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade'); 
            $table->string('delivery_method', 100); 
            $table->string('delivery_address'); 
            $table->dateTime('delivery_date')->nullable(); 
            $table->string('delivery_status', 50)->default('pending'); 
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->onDelete('set null'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};