<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\FridgeItem;

class FridgeScanController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string|max:32',
        ]);

        $barcode = $data['barcode'];

        // Кэшируем ответ, чтобы не дергать API лишний раз
        $off = Cache::remember("off:$barcode", 1440, function () use ($barcode) {
            $url = "https://world.openfoodfacts.org/api/v0/product/{$barcode}.json";
            return Http::timeout(8)->get($url)->json();
        });

        $name = null;
        $brand = null;
        $image = null;

        if (is_array($off) && ($off['status'] ?? 0) === 1) {
            $p = $off['product'] ?? [];
            $name  = $p['product_name'] ?? null;
            $brand = $p['brands'] ?? null;
            $image = $p['image_small_url'] ?? ($p['image_front_small_url'] ?? null);
        }

        // Фоллбэк — если имя не нашли, возьмём сам штрихкод
        $finalName = $name ?: "Товар {$barcode}";

        // Создаём запись в холодильнике (минимум — имя)
        FridgeItem::firstOrCreate(
            ['name' => $finalName],
            [
                'quantity'     => 1,
                'weight_grams' => null,
                'comment'      => $brand ? "Бренд: {$brand}" : null,
                // если в таблице есть поля ниже — положим:
                // 'barcode'   => $barcode,
                // 'image_url' => $image,
            ]
        );

        return back()->with('status', "«{$finalName}» добавлено в холодильник 🧊");
    }
}
