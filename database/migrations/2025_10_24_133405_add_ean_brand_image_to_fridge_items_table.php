<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fridge_items', function (Blueprint $table) {
            // SQLite умеет ADD COLUMN — это ок
            $table->string('ean')->nullable()->after('id');
            $table->string('brand')->nullable()->after('name');
            $table->string('image_url')->nullable()->after('brand');

            // если quantity ещё без default — можно задать:
            // $table->integer('quantity')->default(0)->change(); // В SQLite change() не всегда работает
            // поэтому лучше оставить как есть, а дефолт контролировать в коде
        });

        // Уникальный индекс по ean (в SQLite NULL-значения не конфликтуют)
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->unique('ean', 'fridge_items_ean_unique');
        });
    }

    public function down(): void
    {
        Schema::table('fridge_items', function (Blueprint $table) {
            $table->dropUnique('fridge_items_ean_unique');
            $table->dropColumn(['ean','brand','image_url']);
        });
    }
};
