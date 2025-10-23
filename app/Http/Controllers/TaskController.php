<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        // latest() сортирует по created_at DESC
        $tasks = Task::latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    // Создание новой задачи
    public function store(Request $request)
    {
        // Валидация: обязательный title до 255 символов
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        Task::create([
            'title'   => $validated['title'],
            'is_done' => false,
        ]);

        // redirect()->route(...) + флеш-сообщение
        return redirect()->route('tasks.index')->with('status', 'Задача создана');
    }

    // Переключение флага выполнено/не выполнено
    public function toggle(Task $task)
    {
        $task->is_done = ! $task->is_done;
        $task->save();

        return redirect()->route('tasks.index');
    }

    // Удаление задачи
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('status', 'Задача удалена');
    }
}
