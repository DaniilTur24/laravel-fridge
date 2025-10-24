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

    <div class="fridge-tools">
  <button class="btn btn-secondary" type="button" id="scan-open">
    Сканировать штрих-код
  </button>
</div>




  </form>
  <dialog id="scan-dialog">
  <div class="scan-head">
    <h3>Сканирование</h3>
    <button class="btn btn-secondary" id="scan-close" type="button" aria-label="Закрыть">✕</button>
  </div>
  <div class="scan-body">
    <div id="scan-container" class="scan-container" aria-label="Просмотр камеры"></div>
    <p class="muted" id="scan-hint">Наведите камеру на EAN-13/EAN-8</p>
    <p class="scan-status" id="scan-status" aria-live="polite"></p>
  </div>
  <div class="scan-actions">
    <button class="btn" id="scan-stop" type="button">Стоп</button>
  </div>
</dialog>
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
</section>
@push('scripts')
<script type="module">
  import Quagga from "https://cdn.jsdelivr.net/npm/@ericblade/quagga2/+esm";

  const dlg = document.getElementById('scan-dialog');
  const openBtn = document.getElementById('scan-open');
  const closeBtn = document.getElementById('scan-close');
  const stopBtn = document.getElementById('scan-stop');
  const container = document.getElementById('scan-container'); // <-- новый контейнер
  const statusEl = document.getElementById('scan-status');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  let running = false, lastCode = null, sameReads = 0;

  async function startScanner() {
    statusEl.textContent = 'Инициализация камеры…';

    await Quagga.init({
      inputStream: {
        name: "Live",
        type: "LiveStream",
        target: container,                 // <-- ВАЖНО: не video, а контейнер
        constraints: {
          facingMode: "environment",
          width:  { min: 640, ideal: 1280 },
          height: { min: 480, ideal: 720 }
        }
      },
      decoder: { readers: ["ean_reader","ean_8_reader","upc_reader"] },
      locate: true,
      numOfWorkers: 0, // iOS/Safari стабильнее
      frequency: 5
    });

    Quagga.offDetected(onDetected);
    Quagga.onDetected(onDetected);

    // (необязательно) рамки для дебага
    Quagga.onProcessed(function(result){
      const ctx = Quagga.canvas?.ctx?.overlay;
      const canvas = Quagga.canvas?.dom?.overlay;
      if (!ctx || !canvas) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      if (result?.boxes) {
        result.boxes.filter(b => b !== result.box).forEach(b => {
          Quagga.ImageDebug.drawPath(b, {x:0,y:1}, ctx, {color:"green", lineWidth:2});
        });
      }
      if (result?.box) {
        Quagga.ImageDebug.drawPath(result.box, {x:0,y:1}, ctx, {color:"blue", lineWidth:2});
      }
    });

    await Quagga.start();
    running = true;
    statusEl.textContent = 'Сканируйте штрих-код…';
  }

  function stopScanner() {
    if (running) {
      Quagga.stop();
      Quagga.offDetected(onDetected);
      running = false;
    }
  }

  async function onDetected(res) {
    const code = res?.codeResult?.code;
    if (!code) return;

    if (code === lastCode) sameReads++; else { lastCode = code; sameReads = 1; }
    if (sameReads < 2) return; // двойное подтверждение

    statusEl.textContent = `Найден код: ${code}`;
    stopScanner();

    if (!/^\d{8,14}$/.test(code)) {
      statusEl.textContent = 'Похоже, это не EAN-код.';
      return;
    }

    try {
      const r = await fetch("{{ route('fridge.scan') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrf },
        body: JSON.stringify({ ean: code })
      });
      const json = await r.json();
      if (!json.ok) throw new Error(json.error || 'scan_failed');

      statusEl.textContent = 'Добавлено! Обновляю список…';
      window.location.reload();
    } catch (e) {
      console.error(e);
      statusEl.textContent = 'Ошибка при добавлении. Попробуйте ещё раз.';
      setTimeout(() => { lastCode=null; sameReads=0; startScanner(); }, 1200);
    }
  }

  // UI
  openBtn?.addEventListener('click', async () => {
    dlg.showModal();
    lastCode = null; sameReads = 0;
    await startScanner();
  });
  closeBtn?.addEventListener('click', () => { stopScanner(); dlg.close(); });
  stopBtn?.addEventListener('click', () => { stopScanner(); statusEl.textContent='Остановлено.'; });
  dlg?.addEventListener('click', (e) => {
    const rect = dlg.getBoundingClientRect();
    const inside = e.clientX>=rect.left && e.clientX<=rect.right && e.clientY>=rect.top && e.clientY<=rect.bottom;
    if (!inside) { stopScanner(); dlg.close(); }
  });
</script>
@endpush




@endsection