<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); 
            $table->text('description')->nullable(); 
            $table->decimal('price', 8, 2); 
            $table->integer('stock_quantity')->default(0); 
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('manufacturer_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};