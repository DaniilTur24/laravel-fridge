@extends('layouts.app')

@section('title', 'Редактировать продукт')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/fridge.css') }}">
@endpush


@section('content')
  <h1 class="mb-3">Редактировать продукт</h1>

  <form action="{{ route('fridge.update', $item) }}" method="post" class="row mb-2">
    @csrf
    @method('PUT')

    <input class="input" type="text" name="name" value="{{ old('name', $item->name) }}" placeholder="Название" required>
    <input class="input" type="number" name="quantity" min="1" step="1" value="{{ old('quantity', $item->quantity) }}" placeholder="Кол-во">
    <input class="input" type="number" name="weight_grams" min="1" step="1" value="{{ old('weight_grams', $item->weight_grams) }}" placeholder="Граммы (опц.)">
    <input class="input" type="text" name="comment" value="{{ old('comment', $item->comment) }}" placeholder="Комментарий (опц.)" style="width: 260px;">

    <button class="btn" type="submit">Сохранить</button>
  </form>

  @error('name')         <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('quantity')     <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('weight_grams') <div class="errors mt-1">{{ $message }}</div> @enderror
  @error('comment')      <div class="errors mt-1">{{ $message }}</div> @enderror

  <p class="mt-3"><a class="btn" href="{{ route('fridge.index') }}">← Назад к списку</a></p>
@endsection
