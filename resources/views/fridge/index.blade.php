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

    <button class="btn btn-secondary" type="button" onclick="openScanner()">Сканировать штрих-код</button>

    <!-- Modal -->
    <div id="scanModal" class="card" style="display:none; position:fixed; inset:0; margin:auto; max-width:640px; height:80vh; z-index:1000; overflow:hidden;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
        <h3 class="card-title">Сканируем…</h3>
        <button class="btn btn-ghost" type="button" onclick="closeScanner()">Закрыть</button>
      </div>
      <div id="scanner" style="position:relative; width:100%; height:100%; background:#000;"></div>

      <form id="scanSubmit" action="{{ route('fridge.scan') }}" method="post" style="display:none;">
        @csrf
        <input type="hidden" name="barcode" id="barcodeField">
      </form>
    </div>

    <button type="button" class="btn btn-secondary mt-2" onclick="openScannerZX()">📷 Сканировать штрих-код</button>

    <div id="scanModal" class="card" style="display:none; position:fixed; inset:0; margin:auto; max-width:640px; height:80vh; z-index:1000; overflow:hidden;">
      <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
        <h3 class="card-title">Сканируем…</h3>
        <button class="btn btn-ghost" type="button" onclick="closeScannerZX()">✖</button>
      </div>

      <div style="position:relative; width:100%; height:100%; background:#000; display:flex; align-items:center; justify-content:center;">
        <video id="video-previewer" style="width:100%; height:100%; object-fit:cover;" autoplay muted playsinline webkit-playsinline></video>
        <!-- направляющая рамка -->
        <div style="position:absolute; inset:20% 10%; border:3px solid rgba(255,255,255,.6); border-radius:12px;"></div>
      </div>

      <form id="scanSubmit" action="{{ route('fridge.scan') }}" method="post" style="display:none;">
        @csrf
        <input type="hidden" name="barcode" id="barcodeField">
      </form>

      <div id="scanInfo" class="card-sub" style="padding:10px 14px">Наведи камеру на штрих-код…</div>
    </div>


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
        {{-- Отправить в What to buy --}}
        <form action="{{ route('fridge.toTask', $item) }}" method="post" style="display:inline">
          @csrf
          <button class="btn btn-secondary" type="submit" title="Добавить в список покупок">
            В список покупок
          </button>
        </form>

        {{-- Вариант: Перенести и удалить из холодильника (одним кликом) --}}
        <form action="{{ route('fridge.toTask', $item) }}" method="post" style="display:inline">
          @csrf
          <input type="hidden" name="remove" value="1">
          <button class="btn btn-primary" type="submit" title="Перенести и удалить из холодильника">
            Перенести 🛒
          </button>
        </form>

        <a class="btn btn-secondary" href="{{ route('fridge.edit', $item) }}">Редактировать</a>

        <form action="{{ route('fridge.destroy', $item) }}" method="post"
          onsubmit="return confirm('Удалить {{ $item->name }}?')" style="display:inline">
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
  <script src="https://unpkg.com/@zxing/library@0.20.0"></script>
  <script>
    let zxingReader = null;
    let activeDeviceId = null;

    function openScannerZX() {
      document.getElementById('scanModal').style.display = 'block';
      startZXing();
    }

    function closeScannerZX() {
      stopZXing();
      document.getElementById('scanModal').style.display = 'none';
    }

    async function startZXing() {
      try {
        const codeReader = new ZXing.BrowserMultiFormatReader();
        zxingReader = codeReader;

        // выберем тыловую камеру, если есть
        const devices = await ZXing.BrowserCodeReader.listVideoInputDevices();
        const back = devices.find(d => /back|rear|environment/i.test(d.label)) || devices[0];
        activeDeviceId = back?.deviceId;

        const video = document.getElementById('video-previewer');
        // запускаем декодинг из видеоустройства
        await codeReader.decodeFromVideoDevice(activeDeviceId, video, (result, err) => {
          if (result) {
            onDetectedZX(result.getText());
          } else if (err && !(err instanceof ZXing.NotFoundException)) {
            // это не «ничего не нашли», а реальная ошибка
            console.warn(err);
          }
        });

        document.getElementById('scanInfo').textContent = back ? 'Камера запущена' : 'Камера не найдена';
      } catch (e) {
        console.error('ZXing init error', e);
        document.getElementById('scanInfo').textContent = 'Ошибка доступа к камере. Проверь HTTPS/разрешения.';
      }
    }

    function stopZXing() {
      try {
        zxingReader && zxingReader.reset(); // останавливает стрим и декодер
      } catch (e) {}
      const video = document.getElementById('video-previewer');
      if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(t => t.stop());
        video.srcObject = null;
      }
    }

    function onDetectedZX(text) {
      // Закрываем сканер и шлём форму
      document.getElementById('scanInfo').textContent = 'Найдено: ' + text;
      closeScannerZX();
      setTimeout(() => {
        document.getElementById('barcodeField').value = text;
        document.getElementById('scanSubmit').submit();
      }, 50);
    }
  </script>


</section>
@endsection