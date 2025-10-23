<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\FridgeController;



Route::get('/', function () {
    return view('welcome');
});
Route::get('/hello', [HelloController::class, 'index']);

Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::get('/fridge', [FridgeController::class, 'index'])->name('fridge.index');
Route::post('/fridge', [FridgeController::class, 'store'])->name('fridge.store');
Route::delete('/fridge/{item}', [FridgeController::class, 'destroy'])->name('fridge.destroy');

Route::get('/fridge/{item}/edit', [FridgeController::class, 'edit'])->name('fridge.edit');
Route::put('/fridge/{item}',       [FridgeController::class, 'update'])->name('fridge.update');



