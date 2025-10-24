@extends('layouts.app')
@section('title', 'Мой холодильник')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fridge.css') }}">
@endpush

@section('content')
<section class="card">
  <div class="card-header">
    <div class="fridge-head">
      <h1 class="card-title">Мой холодильник</h1>
      <span class="badge" aria-label="Количество позиций">
        {{ $items->count() }} шт.
      </span>
    </div>
  </div>

  {{-- Форма добавления --}}
  <form action="{{ route('fridge.store') }}" method="post" class="form--stacked fridge-form mb-2" novalidate>
    @csrf

    <div class="field">
      <label class="label" for="name">Название</label>
      <input id="name" class="input" type="text" name="name" placeholder="Например: Молоко" value="{{ old('name') }}" required>
      @error('name') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="quantity">Кол-во</label>
      <input id="quantity" class="input" type="number" name="quantity" min="1" step="1" value="{{ old('quantity', 1) }}">
      @error('quantity') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="weight_grams">Граммы (опц.)</label>
      <input id="weight_grams" class="input" type="number" name="weight_grams" min="1" step="1" value="{{ old('weight_grams') }}">
      @error('weight_grams') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="comment">Комментарий (опц.)</label>
      <input id="comment" class="input" type="text" name="comment" value="{{ old('comment') }}">
      @error('comment') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <button class="btn btn-primary" type="submit">Добавить</button>

  </form>
</section>

{{-- Список продуктов --}}
<section class="card mt-2">
  <div class="card-header">
    <h2 class="card-title">Продукты</h2>
  </div>

  <ul class="fridge-list" role="list">
    @forelse ($items as $item)
    <li class="fridge-item">
      <div class="fridge-item__content">
        <div class="fridge-item__title">
          <strong>{{ $item->name }}</strong>
        </div>
        <div class="fridge-item__meta">
          <span>× {{ $item->quantity }}</span>
          @if($item->weight_grams) <span>• {{ $item->weight_grams }} г</span> @endif
          @if($item->comment) <span class="comment"> {{ $item->comment }} </span> @endif
        </div>
      </div>

      <div class="fridge-actions" role="group" aria-label="Действия">
        <a class="btn btn-secondary" href="{{ route('fridge.edit', $item) }}">Редактировать</a>

        <form action="{{ route('fridge.destroy', $item) }}" method="post"
          onsubmit="return confirm('Удалить {{ $item->name }}?')">
          @csrf
          @method('DELETE')
          <button class="btn btn-danger" type="submit">Удалить</button>
        </form>
      </div>
    </li>
    @empty
    <li class="muted">Пока пусто. Добавь первый продукт ↑</li>
    @endforelse
  </ul>
</section>
@endsection