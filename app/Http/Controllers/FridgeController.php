<?php

namespace App\Http\Controllers;

use App\Models\FridgeItem;
use Illuminate\Http\Request;
use App\Models\Task;

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
}
