<?php

namespace App\Http\Controllers;

use App\Models\FridgeItem;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FridgeController extends Controller
{
    // Список + форма добавления
    public function index()
    {
        $items = FridgeItem::orderBy('created_at', 'desc')->get();

        return view('fridge.index', compact('items'));
    }

    // Добавить продукт
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'quantity'     => ['nullable', 'integer', 'min:1'],
            'weight_grams' => ['nullable', 'integer', 'min:1'],
            'comment'      => ['nullable', 'string', 'max:2000'],
        ]);

        FridgeItem::create([
            'name'         => $data['name'],
            'quantity'     => $data['quantity'] ?? 1,
            'weight_grams' => $data['weight_grams'] ?? null,
            'comment'      => $data['comment'] ?? null,
        ]);

        return redirect()->route('fridge.index')->with('status', 'Продукт добавлен');
    }


    // Удалить продукт
    public function destroy(FridgeItem $item)
    {
        $item->delete();

        return redirect()->route('fridge.index')->with('status', 'Продукт удалён');
    }

    public function edit(FridgeItem $item)
    {
        return view('fridge.edit', compact('item'));
    }

    public function update(Request $request, FridgeItem $item)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'quantity'     => ['required', 'integer', 'min:1'],
            'weight_grams' => ['nullable', 'integer', 'min:1'],
            'comment'      => ['nullable', 'string', 'max:2000'],
        ]);

        $item->update($data);

        return redirect()->route('fridge.index')->with('status', 'Продукт обновлён');
    }

    public function toTask(FridgeItem $item, Request $request)
    {
        // Собираем “человекочитаемое” название с количеством/весом/комментом
        $parts = [$item->name];

        $title = implode(' • ', $parts);

        // Не дублируем одинаковые задачи (если уже есть — просто не создаём вторую)
        Task::firstOrCreate(
            ['title' => $title],
            ['is_done' => false]
        );

        // Опционально: “перенести” — удалить из холодильника, если попросили
        if ($request->boolean('remove')) {
            $item->delete();
            return back()->with('status', 'Перенесено в «What to buy» и удалено из холодильника.');
        }

        return back()->with('status', 'Добавлено в «What to buy».');
    }

    public function scan(Request $request)
{
    $data = $request->validate([
        'ean' => 'required|digits_between:8,14',
    ]);
    $ean = $data['ean'];

    $resp = \Illuminate\Support\Facades\Http::timeout(6)
        ->get("https://world.openfoodfacts.org/api/v2/product/{$ean}.json");

    if (!$resp->ok()) {
        return response()->json(['ok'=>false, 'error'=>'off_unreachable'], 502);
    }

    $json = $resp->json();
    $status = $json['status'] ?? 0;

    if ($status !== 1) {
        // Нет в OFF — заведём пустую карточку по EAN
        $item = \App\Models\FridgeItem::firstOrCreate(
            ['ean' => $ean],
            [
                'name'         => 'Неизвестный продукт',
                'brand'        => null,
                'image_url'    => null,
                'quantity'     => 0,
                'weight_grams' => null,
                'comment'      => null,
            ]
        );
        $item->increment('quantity');
        return response()->json(['ok'=>true, 'fallback'=>true, 'item'=>$item]);
    }

    $p = $json['product'] ?? [];
    $name  = $p['product_name'] ?? ($p['generic_name'] ?? 'Без названия');
    $brand = $p['brands'] ?? null;
    $img   = $p['image_front_small_url'] ?? ($p['image_url'] ?? null);

    // 1) найдём или создадим
    $item = \App\Models\FridgeItem::firstOrCreate(
        ['ean' => $ean],
        [
            'name'         => $name ?: 'Без названия',
            'brand'        => $brand,
            'image_url'    => $img,
            'quantity'     => 0,
            'weight_grams' => null,
            'comment'      => null,
        ]
    );

    // 2) если уже был — обновим метаданные (не трогаем вручную введённые поля)
    $item->fill([
        'name'      => $name ?: $item->name,
        'brand'     => $brand ?? $item->brand,
        'image_url' => $img   ?? $item->image_url,
    ])->save();

    // 3) инкремент количества
    $item->increment('quantity');

    return response()->json(['ok'=>true, 'item'=>$item]);
}

}
