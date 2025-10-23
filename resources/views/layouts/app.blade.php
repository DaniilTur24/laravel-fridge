<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>@yield('title', 'Моё приложение')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>
  <nav class="nav">
    <div class="nav-inner">
      <a href="{{ url('/') }}">Главная</a>
      <a href="{{ route('tasks.index') }}">Задачи</a>
      <a href="{{ route('fridge.index') }}">Холодильник</a>
    </div>
  </nav>

  <main class="container">
    @yield('content')
  </main>
</body>
</html>
