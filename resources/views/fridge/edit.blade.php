@extends('layouts.app')
@section('title', 'Редактировать продукт')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fridge.css') }}">
@endpush

@section('content')
<section class="card">
  <div class="card-header">
    <h1 class="card-title">Редактировать продукт</h1>
  </div>

  <form action="{{ route('fridge.update', $item) }}" method="post" class="form--stacked fridge-form" novalidate>
    @csrf
    @method('PUT')

    <div class="field">
      <label class="label" for="name">Название</label>
      <input id="name" class="input" type="text" name="name" value="{{ old('name', $item->name) }}" required>
      @error('name') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="quantity">Кол-во</label>
      <input id="quantity" class="input" type="number" name="quantity" min="1" step="1" value="{{ old('quantity', $item->quantity) }}">
      @error('quantity') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="weight_grams">Граммы (опц.)</label>
      <input id="weight_grams" class="input" type="number" name="weight_grams" min="1" step="1" value="{{ old('weight_grams', $item->weight_grams) }}">
      @error('weight_grams') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
      <label class="label" for="comment">Комментарий (опц.)</label>
      <input id="comment" class="input" type="text" name="comment" value="{{ old('comment', $item->comment) }}">
      @error('comment') <p class="input-error">{{ $message }}</p> @enderror
    </div>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Сохранить</button>
      <a class="btn btn-ghost" href="{{ route('fridge.index') }}">Отмена</a>
    </div>

  </form>
</section>
@endsection