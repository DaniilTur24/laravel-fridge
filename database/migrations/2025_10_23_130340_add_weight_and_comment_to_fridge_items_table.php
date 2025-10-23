<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->unsignedInteger('weight_grams')->nullable()->after('quantity');
            $table->text('comment')->nullable()->after('weight_grams');
        });
    }

    public function down(): void
    {
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->dropColumn(['weight_grams', 'comment']);
        });
    }
};

