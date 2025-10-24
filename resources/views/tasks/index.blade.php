@extends('layouts.app')

@section('title', 'Мои задачи')

@push('styles')
  {{-- при желании можно вынести нижеописанные стили в отдельный tasks.css --}}
  <link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')
  <section class="card">
    <div class="card-header">
      <h1 class="card-title">Che nado kupit ✅</h1>
    </div>

    {{-- флеш-сообщение --}}
    @if (session('status'))
      <div class="alert alert-success mb-2">{{ session('status') }}</div>
    @endif

    {{-- форма добавления --}}
    <form action="{{ route('tasks.store') }}" method="post" class="form-row mb-2">
      @csrf
      <input class="input" type="text" name="title" placeholder="Новая задача..." value="{{ old('title') }}" required>
      <button class="btn btn-primary" type="submit">Добавить</button>
    </form>

    {{-- ошибки валидации --}}
    @error('title')
      <p class="input-error">{{ $message }}</p>
    @enderror

    {{-- список задач --}}
<div class="task-list mt-2">
  @forelse ($tasks as $task)
    <div class="fridge-item">
      {{-- ТОГГЛ СТАТУСА через кастомный чекбокс --}}
      <form action="{{ route('tasks.toggle', $task) }}" method="post" class="task-toggle-form" style="display:flex; align-items:center; gap:10px">
        @csrf
        @method('PATCH')

        @php($cid = 'cb26-'.$task->id)
        <div class="checkbox-wrapper-26">
          <input
            type="checkbox"
            id="{{ $cid }}"
            name="is_done"
            {{ $task->is_done ? 'checked' : '' }}
            onchange="this.form.submit()"
          >
          <label for="{{ $cid }}">
            <div class="tick_mark"></div>
          </label>
        </div>

        <label for="{{ $cid }}" class="task-title {{ $task->is_done ? 'done' : '' }}" style="cursor:pointer;">
          {{ $task->title }}
        </label>
      </form>

      {{-- УДАЛЕНИЕ --}}
      <form action="{{ route('tasks.destroy', $task) }}" method="post" onsubmit="return confirm('Удалить задачу?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger" type="submit">Удалить</button>
      </form>
    </div>
  @empty
    <p class="muted mt-2">Задач пока нет. Добавь первую выше ↑</p>
  @endforelse
</div>

  </section>
@endsection
