<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор роли
            $table->string('role_name', 50)->unique(); // Название роли (e.g., 'admin', 'client', 'courier')
            $table->timestamps(); // Поля created_at и updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};