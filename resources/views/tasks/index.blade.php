<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Задачи</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 2rem; }
    form { display: inline; }
    .row { display: flex; gap: .5rem; align-items: center; }
    .done { text-decoration: line-through; color: #777; }
    .errors { color: #b00020; margin:.5rem 0; }
    .flash { background:#e6ffed; border:1px solid #b7f5c5; padding:.5rem .75rem; margin-bottom:1rem; }
    .item { padding:.5rem 0; border-bottom: 1px solid #eee; }
    .wrap { max-width: 640px; }
    button { cursor: pointer; }
    input[type="text"] { padding:.4rem .6rem; width: 100%; max-width: 420px; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Мои задачи</h1>

    {{-- флеш-сообщение --}}
    @if (session('status'))
      <div class="flash">{{ session('status') }}</div>
    @endif

    {{-- форма добавления --}}
    <form action="{{ route('tasks.store') }}" method="post" class="row" style="margin-bottom:1rem;">
      @csrf
      <input type="text" name="title" placeholder="Новая задача..." value="{{ old('title') }}">
      <button type="submit">Добавить</button>
    </form>

    {{-- ошибки валидации --}}
    @error('title')
      <div class="errors">{{ $message }}</div>
    @enderror

    {{-- список задач --}}
    <div>
      @forelse ($tasks as $task)
        <div class="item">
          {{-- переключатель выполнено/не выполнено --}}
          <form action="{{ route('tasks.toggle', $task) }}" method="post">
            @csrf
            @method('PATCH')
            <button type="submit" title="Переключить статус">
              {{ $task->is_done ? '☑︎' : '☐' }}
            </button>
          </form>

          <span class="{{ $task->is_done ? 'done' : '' }}">
            {{ $task->title }}
          </span>

          {{-- удалить --}}
          <form action="{{ route('tasks.destroy', $task) }}" method="post" style="float:right;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Удалить задачу?')">Удалить</button>
          </form>
        </div>
      @empty
        <p>Задач пока нет. Добавь первую выше ↑</p>
      @endforelse
    </div>

    <p style="margin-top:1rem;"><a href="{{ url('/') }}">На главную</a></p>
  </div>
</body>
</html>
