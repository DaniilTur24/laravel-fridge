<!doctype html>
<html lang="ru">

<head>
  <meta charset="utf-8" />
  <title>@yield('title', config('app.name', 'Моё приложение'))</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('styles')
  @stack('scripts')
</head>

<body>
  <a class="skip-link" href="#content">Перейти к содержимому</a>

  <header class="site-header">
    <nav class="nav" aria-label="Основная навигация">
      <div class="nav-inner">
        <a href="{{ route('tasks.index') }}">What to buy</a>
        <a href="{{ route('fridge.index') }}">Холодильник</a>
        <span class="right"></span>
      </div>
    </nav>
  </header>

  <main id="content" class="container">
    @if (session('status'))
    <div class="alert alert-success mb-3" role="status">{{ session('status') }}</div>
    @endif

    @yield('content')
  </main>
</body>

</html>