<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparison_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comparison_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['comparison_list_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparison_list_items');
    }
};
