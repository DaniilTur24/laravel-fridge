@extends('layouts.app')

@section('title', 'Задачи')

@push('styles')
  {{-- если хочешь свои стили для tasks --}}
  <link rel="stylesheet" href="{{ asset('css/tasks.css') }}">
@endpush

@section('content')
  <section class="card">
    <div class="card-header">
      <h1 class="card-title">Список задач 📋</h1>
      <p class="card-sub">Это пример страницы задач, использующей общий layout.</p>
    </div>

    <ul class="space-y">
      @foreach ($tasks as $task)
        <li class="card" style="padding: 1rem;">
          <strong>{{ $task->title }}</strong><br>
          <span class="muted text-sm">{{ $task->description }}</span>
        </li>
      @endforeach
    </ul>

    <p class="mt-3">
      <a class="btn btn-primary" href="{{ url('/') }}">← На главную</a>
    </p>
  </section>
@endsection

