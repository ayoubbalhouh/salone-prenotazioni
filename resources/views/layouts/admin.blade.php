<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>@yield('title', 'Gestione') · {{ config('salone.name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin">
<header class="admin-header">
  <div class="container header-inner">
    <a href="{{ route('admin.dashboard') }}" class="logo">Studio <em>Forbice</em> <span class="tag-admin">Gestione</span></a>
    <nav class="nav admin-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'current' : '' }}">Agenda</a>
      <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'current' : '' }}">Servizi</a>
      <a href="{{ route('admin.closures.index') }}" class="{{ request()->routeIs('admin.closures.*') ? 'current' : '' }}">Chiusure</a>
      <a href="{{ route('home') }}" target="_blank">Sito ↗</a>
      <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="link">Esci</button></form>
    </nav>
  </div>
</header>
<main class="container admin-main">
  @if (session('status'))
    <div class="flash">{{ session('status') }}</div>
  @endif
  @yield('content')
</main>
</body>
</html>
