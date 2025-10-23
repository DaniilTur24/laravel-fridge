<?php

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
        Schema::create('fridge_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');                     // название продукта
        $table->unsignedInteger('quantity')->default(1); // количество (по желанию)
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fridge_items');
    }
};
