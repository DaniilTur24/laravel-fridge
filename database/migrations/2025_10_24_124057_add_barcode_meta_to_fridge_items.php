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
        Schema::table('fridge_items', function (Blueprint $t) {
    $t->string('barcode', 32)->nullable()->index();
    $t->string('image_url')->nullable();
    $t->string('brand')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fridge_items', function (Blueprint $t) {
    $t->string('barcode', 32)->nullable()->index();
    $t->string('image_url')->nullable();
    $t->string('brand')->nullable();
});

    }
};
