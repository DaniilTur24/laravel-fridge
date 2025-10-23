@extends('layouts.app')
@section('title', 'Мой холодильник')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/fridge.css') }}">
@endpush


@section('content')
  @if (session('status'))
    <div class="flash mb-3">{{ session('status') }}</div>
  @endif

  <div class="fridge-title">
    <h1>Мой холодильник 🧊</h1>
  </div>

  {{-- Форма добавления --}}
  <form action="{{ route('fridge.store') }}" method="post" class="row mb-3">
    @csrf
    <input class="input" type="text" name="name" placeholder="Например: Молоко" value="{{ old('name') }}" required>
    <input class="input" type="number" name="quantity" min="1" step="1" placeholder="Кол-во" value="{{ old('quantity', 1) }}">
    <input class="input" type="number" name="weight_grams" min="1" step="1" placeholder="Граммы (опц.)" value="{{ old('weight_grams') }}">
    <input class="input" type="text" name="comment" placeholder="Комментарий (опц.)" value="{{ old('comment') }}" style="width: 260px;">
    <button class="btn" type="submit">Добавить</button>
  </form>

  @error('name')         <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('quantity')     <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('weight_grams') <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('comment')      <div class="errors mt-1">{{ $message }}</div> @enderror

  {{-- Список продуктов --}}
  <div class="fridge-list">
    @forelse ($items as $item)
      <div class="fridge-item">
        <div>
          <strong>{{ $item->name }}</strong>
          <div class="fridge-item__meta">
            × {{ $item->quantity }}
            @if($item->weight_grams) • {{ $item->weight_grams }} г @endif
            @if($item->comment) • {{ $item->comment }} @endif
          </div>
        </div>

        <div class="fridge-actions">
          <a class="btn" href="{{ route('fridge.edit', $item) }}">Редактировать</a>
          <form action="{{ route('fridge.destroy', $item) }}" method="post" onsubmit="return confirm('Удалить {{ $item->name }}?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit">Удалить</button>
          </form>
        </div>
      </div>
    @empty
      <p class="mt-3">Пока пусто. Добавь первый продукт ↑</p>
    @endforelse
  </div>
@endsection
